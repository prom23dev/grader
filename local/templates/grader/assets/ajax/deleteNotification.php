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

$notificationId = (int)($_REQUEST['id'] ?? 0);

if (!$notificationId || !CModule::IncludeModule('iblock')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Bad request']);
    \CMain::FinalActions();
    die();
}

$element = CIBlockElement::GetList(
    [],
    [
        'ID' => $notificationId,
        'IBLOCK_ID' => NOTIFICATION_IBLOCK_ID,
        'PROPERTY_USER_ID' => $USER->GetID(),
    ],
    false,
    false,
    ['ID']
)->Fetch();

if (!$element) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Not found']);
    \CMain::FinalActions();
    die();
}

$deleted = CIBlockElement::Delete($notificationId);

echo json_encode(['success' => (bool)$deleted]);

\CMain::FinalActions();
