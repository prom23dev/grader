<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json; charset=utf-8');

global $USER;

if (!check_bitrix_sessid()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Неверный sessid']);
    \CMain::FinalActions();
    die();
}

if (!$USER->IsAuthorized()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    \CMain::FinalActions();
    die();
}

if (!CModule::IncludeModule('iblock')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'iblock module unavailable']);
    \CMain::FinalActions();
    die();
}

set_time_limit(60);

$batchSize = 200;
$deletedInBatch = 0;

$res = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => NOTIFICATION_IBLOCK_ID,
        'PROPERTY_USER_ID' => $USER->GetID(),
    ],
    false,
    ['nTopCount' => $batchSize],
    ['ID']
);
while ($element = $res->Fetch()) {
    if (CIBlockElement::Delete($element['ID'])) {
        $deletedInBatch++;
    }
}

$remaining = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => NOTIFICATION_IBLOCK_ID,
        'PROPERTY_USER_ID' => $USER->GetID(),
    ],
    false,
    false,
    ['ID']
)->SelectedRowsCount();

echo json_encode(['success' => true, 'deleted' => $deletedInBatch, 'remaining' => $remaining]);

\CMain::FinalActions();
