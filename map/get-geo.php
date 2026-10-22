<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

header('Content-Type: application/json; charset=utf-8');

// getGeoHlDataClass() определена глобально в local/php_interface/geoLocation.php
// (подключается через init.php/prolog_before.php).

function fetchGeoItems(string $hlblockName, array $filter = []): array
{
    $dataClass = getGeoHlDataClass($hlblockName);

    $rows = $dataClass::getList([
        'select' => ['ID', 'UF_NAME'],
        'filter' => $filter,
        'order'  => ['UF_NAME' => 'ASC'],
        'cache'  => ['ttl' => 3600],
    ])->fetchAll();

    return array_map(
        fn($row) => ['id' => (int)$row['ID'], 'name' => $row['UF_NAME']],
        $rows
    );
}

$type = $_GET['type'] ?? '';
$result = [];

switch ($type) {
    case 'regions':
        $result = fetchGeoItems('GeoRegion');
        break;

    case 'districts':
        $regionId = (int)($_GET['region_id'] ?? 0);
        if ($regionId > 0) {
            $result = fetchGeoItems('GeoDistrict', ['UF_REGION' => $regionId]);
        }
        break;

    case 'settlements':
        $districtId = (int)($_GET['district_id'] ?? 0);
        if ($districtId > 0) {
            $result = fetchGeoItems('GeoSettlement', ['UF_DISTRICT' => $districtId, 'UF_ACTIVE' => 1]);
        }
        break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);

// D7 ORM's 'cache' option only builds the managed-cache entry in memory;
// it's flushed to disk here (same as epilog_after.php would do), otherwise
// it's silently discarded when this lightweight script ends.
\CMain::FinalActions();
