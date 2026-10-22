<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

header('Content-Type: application/json; charset=utf-8');

// getGeoHlDataClass() определена глобально в local/php_interface/geoLocation.php
// (подключается через init.php/prolog_before.php).

function respond(array $data, int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$request = json_decode(file_get_contents('php://input'), true) ?: [];

$districtId = (int)($request['district_id'] ?? 0);
$name = trim((string)($request['name'] ?? ''));

if ($districtId <= 0 || $name === '') {
    respond(['success' => false, 'error' => 'Не указан район или название населённого пункта'], 400);
}

$districtClass = getGeoHlDataClass('GeoDistrict');
$district = $districtClass::getList([
    'select' => ['ID'],
    'filter' => ['ID' => $districtId],
])->fetch();

if (!$district) {
    respond(['success' => false, 'error' => 'Район не найден'], 404);
}

$settlementClass = getGeoHlDataClass('GeoSettlement');

$existing = $settlementClass::getList([
    'select' => ['ID', 'UF_ACTIVE'],
    'filter' => ['UF_DISTRICT' => $districtId, '=UF_NAME' => $name],
])->fetch();

if ($existing) {
    respond([
        'success' => true,
        'id'      => (int)$existing['ID'],
        'name'    => $name,
        'active'  => (bool)$existing['UF_ACTIVE'],
        'existed' => true,
    ]);
}

$result = $settlementClass::add([
    'UF_NAME'     => $name,
    'UF_DISTRICT' => $districtId,
    'UF_ACTIVE'   => 0,
]);

if (!$result->isSuccess()) {
    respond(['success' => false, 'error' => implode('; ', $result->getErrorMessages())], 500);
}

respond([
    'success' => true,
    'id'      => $result->getId(),
    'name'    => $name,
    'active'  => false,
    'existed' => false,
]);
