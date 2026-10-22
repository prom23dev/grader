<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

/**
 * Бегущая строка объявлений в шапке (header.php), с привязкой к гео
 * посетителя. См. memory city_selection_mechanism / geo_hlblock_migration.
 *
 * Текущий город посетителя резолвится через resolveCurrentCity()
 * (geoLocation.php): ручной выбор из cookie SELECTED_CITY_ID, иначе
 * автоопределение по IP, иначе дефолт без привязки к реальному элементу
 * GeoSettlement — в этом случае ниже показываются только объявления без
 * гео-привязки.
 */

function getHeaderAnnounceIblockId(): int
{
    return (int)Option::get('main', 'grader_header_announce_iblock_id');
}

function getCurrentCityGeoIds(): array
{
    $empty = ['region' => 0, 'district' => 0, 'settlement' => 0];

    $settlementId = (int)resolveCurrentCity()['id'];
    if ($settlementId <= 0) {
        return $empty;
    }

    Loader::includeModule('highloadblock');

    $settlementClass = getGeoHlDataClass('GeoSettlement');
    $settlement = $settlementClass::getList([
        'select' => ['ID', 'UF_DISTRICT'],
        'filter' => ['ID' => $settlementId],
        'cache'  => ['ttl' => 3600],
    ])->fetch();
    if (!$settlement) {
        return $empty;
    }

    $districtId = (int)$settlement['UF_DISTRICT'];
    $regionId = 0;
    if ($districtId > 0) {
        $districtClass = getGeoHlDataClass('GeoDistrict');
        $district = $districtClass::getList([
            'select' => ['UF_REGION'],
            'filter' => ['ID' => $districtId],
            'cache'  => ['ttl' => 3600],
        ])->fetch();
        $regionId = $district ? (int)$district['UF_REGION'] : 0;
    }

    return ['region' => $regionId, 'district' => $districtId, 'settlement' => $settlementId];
}

/**
 * DB-уровневый фильтр для CIBlockElement::GetList / bitrix:news.list,
 * отбирающий объявления, актуальные для текущего города посетителя: на
 * каждом из трёх уровней (регион/район/нп) гео-свойство объявления либо
 * совпадает с текущим значением, либо не задано (= показывается всем).
 * Та же логика, что в getActiveHeaderAnnouncements(), но как SQL-фильтр —
 * нужен там, где результат должен постранично пагинироваться (/announcements/).
 *
 * @return array
 */
function buildAnnounceGeoDbFilter(): array
{
    $geo = getCurrentCityGeoIds();

    $filter = [];
    foreach (['REGION' => 'region', 'DISTRICT' => 'district', 'SETTLEMENT' => 'settlement'] as $propSuffix => $geoKey) {
        $propCode = 'PROPERTY_PR_ANNOUNCE_' . $propSuffix;
        if ($geo[$geoKey] > 0) {
            $filter[] = [
                'LOGIC' => 'OR',
                ['=' . $propCode => false],
                [$propCode => $geo[$geoKey]],
            ];
        } else {
            $filter['=' . $propCode] = false;
        }
    }

    return $filter;
}

/**
 * Есть ли хотя бы одно активное объявление, проходящее гео-фильтр из
 * buildAnnounceGeoDbFilter(). Используется на /announcements/, чтобы при
 * отсутствии объявлений для текущего города показывать список без фильтра,
 * а не пустую страницу.
 */
function hasActiveAnnouncements(array $geoFilter): bool
{
    $iblockId = getHeaderAnnounceIblockId();
    if ($iblockId <= 0) {
        return false;
    }

    Loader::includeModule('iblock');

    $filter = array_merge(
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y'],
        $geoFilter
    );

    return (bool)\CIBlockElement::GetList([], $filter, false, false, ['ID'])->Fetch();
}

/**
 * Активные (по ACTIVE_FROM/ACTIVE_TO) объявления шапки, актуальные для
 * текущего города посетителя: гео-свойство объявления либо совпадает с
 * текущим уровнем, либо не задано (= показывается всем).
 *
 * @return array<int, array{text: string, link: string}>
 */
function getActiveHeaderAnnouncements(): array
{
    $iblockId = getHeaderAnnounceIblockId();
    if ($iblockId <= 0) {
        return [];
    }

    Loader::includeModule('iblock');

    $geo = getCurrentCityGeoIds();

    $items = \CIBlockElement::GetList(
        ['SORT' => 'ASC', 'ID' => 'ASC'],
        ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y'],
        false,
        false,
        [
            'ID', 'NAME',
            'PROPERTY_ANNOUNCE_LINK',
            'PROPERTY_PR_ANNOUNCE_REGION',
            'PROPERTY_PR_ANNOUNCE_DISTRICT',
            'PROPERTY_PR_ANNOUNCE_SETTLEMENT',
        ]
    );

    $result = [];
    while ($row = $items->Fetch()) {
        $regionProp = (int)$row['PROPERTY_PR_ANNOUNCE_REGION_VALUE'];
        $districtProp = (int)$row['PROPERTY_PR_ANNOUNCE_DISTRICT_VALUE'];
        $settlementProp = (int)$row['PROPERTY_PR_ANNOUNCE_SETTLEMENT_VALUE'];

        // Объявление подходит, если оно адресовано выбранному населенному
        // пункту либо одному из его административных уровней.
        $matchesCity = ($settlementProp > 0 && $settlementProp === $geo['settlement']);
        $matchesDistrict = ($districtProp > 0 && $districtProp === $geo['district']);
        $matchesRegion = ($regionProp > 0 && $regionProp === $geo['region']);

        // Не показываем в бегущей строке объявления без гео-привязки:
        // после выбора города остаются только релевантные ему записи.
        if (!$matchesCity && !$matchesDistrict && !$matchesRegion) {
            continue;
        }

        // Ссылка объявления: либо явно заданная в свойстве ANNOUNCE_LINK
        // (внешний URL), либо по умолчанию — собственная страница объявления
        // в разделе /announcements/ (см. announcements/index.php).
        $customLink = (string)$row['PROPERTY_ANNOUNCE_LINK_VALUE'];
        $result[] = [
            'text' => $row['NAME'],
            'link' => $customLink !== '' ? $customLink : '/announcements/' . (int)$row['ID'] . '/',
        ];
    }

    return $result;
}
