<?php

$rawInput = $_POST ? null : file_get_contents('php://input');

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
\Bitrix\Main\Loader::includeModule('iblock');

header('Content-Type: application/json');

function respondError($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

global $USER;
if (!$USER->IsAuthorized()) {
    respondError('Нужно авторизоваться', 403);
}

$input = $_POST ?: (json_decode($rawInput, true) ?: []);

$projectId = (int)($input['projectId'] ?? 0);
$text = trim((string)($input['text'] ?? ''));

if ($projectId <= 0) {
    respondError('Не указан проект');
}
if ($text === '') {
    respondError('Укажите текст возражения');
}

$project = \CIBlockElement::GetList([], ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'ID' => $projectId], false, false, ['ID'])
    ->GetNextElement();
if (!$project) {
    respondError('Проект не найден', 404);
}
$props = $project->GetProperties();
$reportDateValue = $props['PR_REPORT_DATE']['VALUE'] ?? '';

$userId = (int)$USER->GetID();
if (!canSubmitObjection($userId, $projectId, $reportDateValue)) {
    respondError('Возражение по этому проекту сейчас недоступно', 403);
}

$objectionsIblockId = getObjectionsIblockId();
if ($objectionsIblockId <= 0) {
    respondError('Раздел возражений не настроен', 500);
}

$propValues = [
    'PROJECT_ID' => $projectId,
    'USER_ID' => $userId,
    'TEXT' => $text,
];

if (!empty($_FILES['files']['name'][0])) {
    $files = [];
    \CFile::ConvertFilesToPost($_FILES['files'], $files);
    $propValues['FILES'] = $files;
}

$el = new \CIBlockElement();
$id = $el->Add([
    'IBLOCK_ID' => $objectionsIblockId,
    'NAME' => 'Возражение по проекту #' . $projectId . ' от пользователя #' . $userId,
    'ACTIVE' => 'Y',
    'MODIFIED_BY' => $userId,
    'PROPERTY_VALUES' => $propValues,
]);

if (!$id) {
    respondError('Не удалось сохранить возражение: ' . $el->LAST_ERROR, 500);
}

echo json_encode(['success' => true, 'id' => $id]);
