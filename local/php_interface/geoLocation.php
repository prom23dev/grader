<?php

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

/**
 * Привязка свойств проекта PR_LOCATION/PR_LOCATION_DISTRICT/PR_LOCATION_SETTLEMENT
 * к элементам HL-блоков GeoRegion/GeoDistrict/GeoSettlement (см. memory
 * geo_hlblock_migration). Свойства остаются обычными строковыми свойствами
 * инфоблока, но хранят ID элемента HL-блока вместо свободного текста.
 *
 * Для обратной совместимости с ещё не мигрированными/неоднозначными
 * старыми значениями getGeoName() отдаёт исходную строку как есть, если
 * она не является числовым ID существующего элемента.
 */

function getGeoHlDataClass(string $hlblockName): string
{
    static $cache = [];

    if (!isset($cache[$hlblockName])) {
        Loader::includeModule('highloadblock');
        $hlblock = HighloadBlockTable::getRow([
            'filter' => ['NAME' => $hlblockName],
            'cache'  => ['ttl' => 3600],
        ]);

        $cache[$hlblockName] = HighloadBlockTable::compileEntity($hlblock)->getDataClass();
    }

    return $cache[$hlblockName];
}

/**
 * Находит ID элемента HL-блока по значению, присланному с фронта: либо это
 * уже числовой ID существующего элемента, либо свободный текст (новый
 * вариант, который пользователь ввёл вручную через select2 tags) — тогда
 * ищем по имени в пределах родителя и создаём новый элемент, если не нашли.
 *
 * @param string $hlblockName Имя HL-блока (GeoRegion/GeoDistrict/GeoSettlement)
 * @param string $value ID или название
 * @param array $parentFilter Доп. фильтр по родителю (например ['UF_REGION' => 5])
 * @param array $extraFieldsOnCreate Доп. поля при создании нового элемента (например ['UF_ACTIVE' => 0])
 * @return int ID элемента HL-блока, 0 если значение пустое
 */
function resolveGeoHlId(string $hlblockName, string $value, array $parentFilter = [], array $extraFieldsOnCreate = []): int
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }

    $dataClass = getGeoHlDataClass($hlblockName);

    if (ctype_digit($value)) {
        $existingById = $dataClass::getList([
            'select' => ['ID'],
            'filter' => array_merge(['ID' => (int)$value], $parentFilter),
        ])->fetch();
        if ($existingById) {
            return (int)$existingById['ID'];
        }
    }

    $existingByName = $dataClass::getList([
        'select' => ['ID'],
        'filter' => array_merge(['=UF_NAME' => $value], $parentFilter),
    ])->fetch();
    if ($existingByName) {
        return (int)$existingByName['ID'];
    }

    $result = $dataClass::add(array_merge(
        ['UF_NAME' => $value],
        $parentFilter,
        $extraFieldsOnCreate
    ));

    return $result->isSuccess() ? (int)$result->getId() : 0;
}

function resolveGeoRegionId(string $value): int
{
    return resolveGeoHlId('GeoRegion', $value);
}

function resolveGeoDistrictId(string $value, int $regionId): int
{
    if ($regionId <= 0) {
        return 0;
    }
    return resolveGeoHlId('GeoDistrict', $value, ['UF_REGION' => $regionId]);
}

function resolveGeoSettlementId(string $value, int $districtId): int
{
    if ($districtId <= 0) {
        return 0;
    }
    // Новые населённые пункты создаются неактивными — та же модерация,
    // что и для ручного добавления на карте (map/add-settlement.php).
    return resolveGeoHlId('GeoSettlement', $value, ['UF_DISTRICT' => $districtId], ['UF_ACTIVE' => 0]);
}

/**
 * Резолвит значение свойства проекта (ID элемента HL-блока) в читаемое
 * название. Если значение не является числовым ID или элемент не найден
 * (старые немигрированные проекты), возвращает значение как есть — оно и
 * так хранит свободный текст в этом случае.
 */
function getGeoName(string $hlblockName, ?string $value): string
{
    $value = trim((string)$value);
    if ($value === '' || !ctype_digit($value)) {
        return $value;
    }

    $dataClass = getGeoHlDataClass($hlblockName);
    $row = $dataClass::getList([
        'select' => ['UF_NAME'],
        'filter' => ['ID' => (int)$value],
        'cache'  => ['ttl' => 3600],
    ])->fetch();

    return $row ? $row['UF_NAME'] : $value;
}

function getGeoRegionName(?string $value): string
{
    return getGeoName('GeoRegion', $value);
}

function getGeoDistrictName(?string $value): string
{
    return getGeoName('GeoDistrict', $value);
}

function getGeoSettlementName(?string $value): string
{
    return getGeoName('GeoSettlement', $value);
}

/**
 * Возвращает путь к гербу населённого пункта из GeoSettlement.
 * Значение принимается как ID записи справочника; для совместимости с
 * проектами без привязанного герба возвращается пустая строка.
 */
function getGeoSettlementCoatPath(?string $value): string
{
    $id = (int)$value;
    if ($id <= 0 || !class_exists('CFile')) {
        return '';
    }

    try {
        $dataClass = getGeoHlDataClass('GeoSettlement');
        $row = $dataClass::getList([
            'select' => ['UF_COAT_OF_ARMS'],
            'filter' => ['=ID' => $id],
            'cache'  => ['ttl' => 3600],
        ])->fetch();
        $fileId = (int)($row['UF_COAT_OF_ARMS'] ?? 0);

        return $fileId > 0 ? (string)CFile::GetPath($fileId) : '';
    } catch (\Throwable $e) {
        return '';
    }
}

/**
 * Ищет активный населённый пункт по точному названию (с учётом е/ё),
 * используется для сопоставления ответа GeoIP с элементом GeoSettlement.
 */
function findGeoSettlementIdByName(string $name): ?int
{
    $name = trim($name);
    if ($name === '') {
        return null;
    }

    $dataClass = getGeoHlDataClass('GeoSettlement');

    $row = $dataClass::getList([
        'select' => ['ID'],
        'filter' => ['=UF_NAME' => $name, 'UF_ACTIVE' => 1],
    ])->fetch();
    if ($row) {
        return (int)$row['ID'];
    }

    $altName = str_replace(['Ё', 'ё'], ['Е', 'е'], $name);
    if ($altName !== $name) {
        $row = $dataClass::getList([
            'select' => ['ID'],
            'filter' => ['=UF_NAME' => $altName, 'UF_ACTIVE' => 1],
        ])->fetch();
        if ($row) {
            return (int)$row['ID'];
        }
    }

    return null;
}

/**
 * Определяет город посетителя по IP через встроенный в ядро обработчик
 * SypexGeo (Bitrix\Main\Service\GeoIp\SypexGeo) — он ходит за данными на
 * бесплатный публичный api.sypexgeo.net и не требует локальной базы
 * GeoIP2/MaxMind (.mmdb/.dat), которой в инсталляции нет. Используется
 * напрямую (минуя Manager::getDataResult), т.к. Manager включает обработчики
 * только если для них есть активная запись в b_geoip_handlers.
 */
function detectCityByIp(): ?array
{
    if (!\Bitrix\Main\Loader::includeModule('main')) {
        return null;
    }

    try {
        $ip = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
        if ($ip === '') {
            return null;
        }

        $ipAddress = new \Bitrix\Main\Web\IpAddress($ip);
        if (!$ipAddress->isIPv4() || $ipAddress->isPrivate()) {
            return null;
        }

        $handler = new \Bitrix\Main\Service\GeoIp\SypexGeo(['ACTIVE' => 'Y']);
        $result = $handler->getDataResult($ip, 'ru');
        if (!$result || !$result->isSuccess()) {
            return null;
        }

        $cityName = trim((string)$result->getGeoData()->cityName);
        if ($cityName === '') {
            return null;
        }

        $settlementId = findGeoSettlementIdByName($cityName);
        if (!$settlementId) {
            return null;
        }

        return ['id' => $settlementId, 'name' => getGeoSettlementName((string)$settlementId)];
    } catch (\Throwable $e) {
        return null;
    }
}

/**
 * Текущий город посетителя: ручной выбор из cookie SELECTED_CITY_ID, иначе —
 * автоопределение по IP, иначе — дефолтная "Москва".
 *
 * Автоопределённый город НЕ пишется в cookie автоматически — возвращается
 * с флагом needsConfirm=true, чтобы фронт показал попап-подтверждение
 * ("Ваш город — X?"). Cookie ставится только явным действием пользователя:
 * подтверждением (кнопка "Да") или выбором города в попапе поиска.
 */
function resolveCurrentCity(): array
{
    static $city = null;
    if ($city !== null) {
        return $city;
    }

    $cookieId = isset($_COOKIE['SELECTED_CITY_ID']) && ctype_digit((string)$_COOKIE['SELECTED_CITY_ID'])
        ? (int)$_COOKIE['SELECTED_CITY_ID']
        : 0;

    if ($cookieId > 0) {
        $name = getGeoSettlementName((string)$cookieId);
        if ($name !== (string)$cookieId) {
            $city = ['id' => $cookieId, 'name' => $name, 'needsConfirm' => false];
            return $city;
        }
    }

    $detected = detectCityByIp();
    if ($detected) {
        $city = $detected + ['needsConfirm' => true];
        return $city;
    }

    $city = ['id' => 0, 'name' => 'Москва', 'needsConfirm' => false];
    return $city;
}
