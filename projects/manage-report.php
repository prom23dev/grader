<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_DIR . "front/css/manage-report.css");

global $USER;

function userIsProjectManager(): bool
{
    global $USER;
    if (!$USER->IsAuthorized()) {
        return false;
    }
    if ($USER->IsAdmin()) {
        return true;
    }
    $groups = $USER->GetUserGroupArray();
    $managerGroup = CGroup::GetList($b = 'c_sort', $o = 'asc', ['STRING_ID' => 'MANAGER'])->Fetch();
    return $managerGroup && in_array((int)$managerGroup['ID'], $groups, true);
}

if (!userIsProjectManager()) {
    echo '<div class="container"><p>Доступ только для менеджеров проектов.</p></div>';
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
    return;
}

$projectId = (int)($_GET['id'] ?? 0);
$project = $projectId > 0
    ? CIBlockElement::GetList([], ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'ID' => $projectId], false, false, ['ID', 'NAME'])->GetNextElement()
    : null;

if (!$project) {
    echo '<div class="container"><p>Проект не найден.</p></div>';
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
    return;
}

$props = $project->GetProperties();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid()) {
    $reportText = trim((string)($_POST['report_text'] ?? ''));
    $reportAmount = trim((string)($_POST['report_amount'] ?? ''));

    $propValues = [
        'PR_REPORT_TEXT' => $reportText,
        'PR_REPORT_AMOUNT' => $reportAmount,
    ];

    if (!empty($_FILES['photos']['name'][0])) {
        $files = [];
        CFile::ConvertFilesToPost($_FILES['photos'], $files);
        $propValues['PR_REPORT_PHOTOS'] = $files;
    }

    if (empty($props['PR_REPORT_DATE']['VALUE'])) {
        $propValues['PR_REPORT_DATE'] = date('d.m.Y H:i:s');
    }

    $statusEnum = CIBlockPropertyEnum::GetList([], [
        'IBLOCK_ID' => PROJECTS_IBLOCK_ID,
        'CODE' => 'PR_STATUS',
        'VALUE' => 'Реализован',
    ])->Fetch();
    if ($statusEnum) {
        $propValues['PR_STATUS'] = $statusEnum['ID'];
    }

    $el = new CIBlockElement();
    $updated = $el->Update($projectId, [
        'IBLOCK_ID' => PROJECTS_IBLOCK_ID,
        'MODIFIED_BY' => $USER->GetID(),
        'PROPERTY_VALUES' => $propValues,
    ]);

    if ($updated) {
        $message = 'Отчёт опубликован, проект переведён в статус «Реализован».';
        $res = CIBlockElement::GetList([], ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'ID' => $projectId], false, false, ['ID'])->GetNextElement();
        $props = $res->GetProperties();
    } else {
        $message = 'Ошибка: ' . $el->LAST_ERROR;
    }
}
?>
<div class="manage-report">
    <h1><?= htmlspecialchars($project->GetFields()['NAME']) ?></h1>
    <?php if ($message): ?>
        <p class="manage-report__message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <p class="manage-report__status">Текущий статус: <?= htmlspecialchars($props['PR_STATUS']['VALUE'] ?? '') ?></p>
    <form method="post" enctype="multipart/form-data">
        <?= bitrix_sessid_post() ?>
        <div class="manage-report__field">
            <label for="report_text">Описание выполненных работ</label>
            <textarea name="report_text" id="report_text" rows="8"><?= htmlspecialchars($props['PR_REPORT_TEXT']['VALUE'] ?? '') ?></textarea>
        </div>
        <div class="manage-report__field">
            <label for="report_amount">Итоговая сумма расходов</label>
            <input type="text" name="report_amount" id="report_amount" value="<?= htmlspecialchars($props['PR_REPORT_AMOUNT']['VALUE'] ?? '') ?>">
        </div>
        <div class="manage-report__field">
            <label for="photos">Фотографии «после»</label>
            <input type="file" name="photos[]" id="photos" multiple accept="image/*">
        </div>
        <button type="submit" class="manage-report__submit">Опубликовать отчёт и перевести в «Реализован»</button>
    </form>
</div>
<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
