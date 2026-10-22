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

$enabled = ($_REQUEST['enabled'] ?? '') === '1' ? 1 : 0;

$result = $USER->Update($USER->GetID(), ['UF_EMAIL_NOTIFY' => $enabled]);

echo json_encode(['success' => (bool)$result, 'error' => $result ? '' : $USER->LAST_ERROR]);

\CMain::FinalActions();
