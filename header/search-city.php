<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json; charset=utf-8');

// getGeoHlDataClass() определена глобально в local/php_interface/geoLocation.php
// (подключается через init.php/prolog_before.php).

$query = trim((string)($_GET['q'] ?? ''));
$result = [];

if ($query !== '') {
    $settlementClass = getGeoHlDataClass('GeoSettlement');
    $districtClass = getGeoHlDataClass('GeoDistrict');
    $regionClass = getGeoHlDataClass('GeoRegion');

    $rows = $settlementClass::getList([
        'select' => ['ID', 'UF_NAME', 'UF_DISTRICT', 'UF_POPULATION'],
        'filter' => ['UF_ACTIVE' => 1, '%UF_NAME' => $query],
        // Сначала наиболее населённые города, затем населённые пункты без
        // значения населения (внутри групп — стабильная сортировка по имени).
        'order'  => ['UF_POPULATION' => 'DESC', 'UF_NAME' => 'ASC', 'ID' => 'ASC'],
        // Берём с запасом: после добавления региона часть строк может
        // схлопнуться в один отображаемый город.
        'limit'  => 100,
    ])->fetchAll();

    // Приоритет целого слова запроса выше населения: «Выселки» должны
    // опережать, например, «Большие Выселки района ...». Внутри групп
    // сохраняем сортировку по населению и имени.
    $normalizedQuery = mb_strtolower($query, 'UTF-8');
    $wordPattern = '/(?<![\p{L}\p{N}])' . preg_quote($normalizedQuery, '/') . '(?![\p{L}\p{N}])/iu';
    usort($rows, static function (array $a, array $b) use ($normalizedQuery, $wordPattern): int {
        $aName = mb_strtolower((string)$a['UF_NAME'], 'UTF-8');
        $bName = mb_strtolower((string)$b['UF_NAME'], 'UTF-8');
        $aExact = $aName === $normalizedQuery ? 2 : (preg_match($wordPattern, $aName) ? 1 : 0);
        $bExact = $bName === $normalizedQuery ? 2 : (preg_match($wordPattern, $bName) ? 1 : 0);
        if ($aExact !== $bExact) {
            return $bExact <=> $aExact;
        }
        $aPopulation = (int)($a['UF_POPULATION'] ?? 0);
        $bPopulation = (int)($b['UF_POPULATION'] ?? 0);
        if ($aPopulation !== $bPopulation) {
            return $bPopulation <=> $aPopulation;
        }
        return strcasecmp((string)$a['UF_NAME'], (string)$b['UF_NAME']);
    });

    $districtIds = array_unique(array_filter(array_column($rows, 'UF_DISTRICT')));
    $regionNameByDistrictId = [];
    if ($districtIds) {
        $districtRows = $districtClass::getList([
            'select' => ['ID', 'UF_REGION'],
            'filter' => ['ID' => $districtIds],
        ])->fetchAll();

        $regionIds = array_unique(array_filter(array_column($districtRows, 'UF_REGION')));
        $regionNameById = [];
        if ($regionIds) {
            $regionRows = $regionClass::getList([
                'select' => ['ID', 'UF_NAME'],
                'filter' => ['ID' => $regionIds],
            ])->fetchAll();
            foreach ($regionRows as $regionRow) {
                $regionNameById[$regionRow['ID']] = $regionRow['UF_NAME'];
            }
        }

        foreach ($districtRows as $districtRow) {
            $regionNameByDistrictId[$districtRow['ID']] = $regionNameById[$districtRow['UF_REGION']] ?? '';
        }
    }

    $nameCounts = array_count_values(array_column($rows, 'UF_NAME'));

    $seenNames = [];
    foreach ($rows as $row) {
            $name = $row['UF_NAME'];
            $regionName = $regionNameByDistrictId[$row['UF_DISTRICT']] ?? '';
            if ($nameCounts[$name] > 1 && $regionName !== '') {
                $name .= ' (' . $regionName . ')';
            }
            // В исходном справочнике встречаются повторные строки одного и
            // того же населённого пункта (например, «Москва» в районах
            // Москвы). Пользователю показываем один вариант, сохраняя
            // первую запись — она уже отсортирована по населению.
            if (isset($seenNames[$name])) {
                continue;
            }
            $seenNames[$name] = true;
            $result[] = ['id' => (int)$row['ID'], 'name' => $name];
            if (count($result) >= 20) {
                break;
            }
    }
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

\CMain::FinalActions();
