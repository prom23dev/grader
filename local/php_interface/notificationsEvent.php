<?php

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAdd");

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdate");

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdate");

function addNotification($notificationName, $arFields)
{
    global $USER;
    $res = CIBlockElement::GetByID($arFields['ID']);
    if ($ar_res = $res->GetNext()) {
        $userId = $ar_res['CREATED_BY'];
    }
    if (!$userId) {
        return;
    }
    $el = new CIBlockElement;

    $code = CUtil::translit($notificationName, "ru");
    $filter = [
        "IBLOCK_ID" => NOTIFICATION_IBLOCK_ID,
        "CODE" => $code,
        "PROPERTY_USER_ID" => $userId,
        "PROPERTY_VIEWED" => "N",
    ];
    $existing = CIBlockElement::GetList([], $filter, false, false, ["ID"])->Fetch();

    $arrLoad = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_ID" => NOTIFICATION_IBLOCK_ID,
        "PROPERTY_VALUES" => array("USER_ID" => $userId, "VIEWED" => "N"),
        "NAME" => $notificationName,
        "CODE" => $code,
        "ACTIVE" => "Y",
    );
    // Обработчик вызывается внутри ajax-запросов (например, createProject.php),
    // поэтому ничего не выводим в ответ — только пишем ошибки в лог
    if ($existing) {
        // Обновляем существующее уведомление
        if (!$el->Update($existing["ID"], $arrLoad)) {
            AddMessage2Log('Ошибка обновления уведомления ' . $existing["ID"] . ': ' . $el->LAST_ERROR, 'notifications');
        }
    } else {
        // Добавляем новое уведомление
        if (!$el->Add($arrLoad)) {
            AddMessage2Log('Ошибка создания уведомления: ' . $el->LAST_ERROR, 'notifications');
        }
    }

}

function getManagerGroupEmails(): array
{
    global $DB;
    $emails = [];
    $res = $DB->Query("
        SELECT U.EMAIL
        FROM b_user U
        INNER JOIN b_user_group UG ON UG.USER_ID = U.ID
        INNER JOIN b_group G ON G.ID = UG.GROUP_ID
        WHERE G.STRING_ID = 'MANAGER' AND U.ACTIVE = 'Y' AND U.EMAIL != ''
    ");
    while ($row = $res->Fetch()) {
        $emails[mb_strtolower($row['EMAIL'])] = $row['EMAIL'];
    }
    return array_values($emails);
}

function sendProjectStatusChangeEmails($arFields, $oldStatusText, $newStatusText)
{
    $projectId = (int)$arFields['ID'];

    $recipients = [];

    // Автор проекта
    $res = CIBlockElement::GetByID($projectId);
    $project = $res->GetNext();
    $projectName = $project['NAME'] ?? $arFields['NAME'] ?? '';
    if ($project && $project['CREATED_BY']) {
        $author = CUser::GetByID($project['CREATED_BY'])->Fetch();
        $authorWantsEmail = !isset($author['UF_EMAIL_NOTIFY']) || (int)$author['UF_EMAIL_NOTIFY'] === 1;
        if ($author && !empty($author['EMAIL']) && $authorWantsEmail) {
            $recipients[mb_strtolower($author['EMAIL'])] = $author['EMAIL'];
        }
    }

    // Участники, поддержавшие проект
    foreach (getProjectDonorEmails($projectId) as $email) {
        $recipients[mb_strtolower($email)] = $email;
    }

    // Менеджеры
    foreach (getManagerGroupEmails() as $email) {
        $recipients[mb_strtolower($email)] = $email;
    }

    if (!$recipients) {
        return;
    }

    $url = '';
    if ($project) {
        $sectionCode = '';
        if (!empty($project['IBLOCK_SECTION_ID'])) {
            $section = CIBlockSection::GetByID($project['IBLOCK_SECTION_ID'])->Fetch();
            $sectionCode = $section['CODE'] ?? '';
        }
        $path = '/projects/' . ($sectionCode ? $sectionCode . '/' : '') . $project['CODE'] . '/';
        $url = (CMain::IsHTTPS() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $path;
    }

    foreach ($recipients as $email) {
        CEvent::Send('grader_project_status_changed', 's1', [
            'PROJECT_NAME' => $projectName,
            'OLD_STATUS' => $oldStatusText,
            'NEW_STATUS' => $newStatusText,
            'PROJECT_URL' => $url,
            'EMAIL_TO' => $email,
        ]);
    }
}

function OnBeforeIBlockElementUpdate(&$arFields)
{

    if ($arFields['IBLOCK_ID'] == 1) {
        // Получаем старые значения элемента
        $res = CIBlockElement::GetByID($arFields['ID']);
        if ($oldElement = $res->GetNextElement()) {
            $oldFields = $oldElement->GetFields();
            $oldProps = $oldElement->GetProperties();
            // Старый статус
            $oldStatusNum = $oldProps['PR_STATUS']['VALUE_ENUM_ID'] ?? '';
            // Новый статус: берём из PROPERTY_VALUES (программные CIBlockElement::Update),
            // либо из $_REQUEST (сохранение через стандартную админ-форму иблоков)
            $newStatusNum = $arFields['PROPERTY_VALUES']['PR_STATUS']
                ?? $_REQUEST['PROP']['16'][0]
                ?? '';
            if (is_array($newStatusNum)) {
                $newStatusNum = reset($newStatusNum);
            }

            if ($newStatusNum != $oldStatusNum) {
                $notificationName = '';
                switch ($newStatusNum) {
                    case 6: //Ошибка
                        $notificationName = "Ваш " . $arFields["NAME"] . " не прошел проверку.";
                        break;
                    case 7: //На проверке
                        $notificationName = $arFields["NAME"] . " отправлен на  повторную модерацию. После проверки данных вам придёт уведомление о создании заявки";
                        break;
                    case 8: //Реализация
                        $notificationName = "Ваш " . $arFields["NAME"] . " прошел проверку.";
                        break;
                    case 9: //Завершён
                        $notificationName = "Сбор денежных средств на ваш " . $arFields["NAME"] . " завершен";
                        break;
                    default:
                        break;
                }
                addNotification($notificationName, $arFields);

                $oldStatusText = $oldProps['PR_STATUS']['VALUE'] ?? '';
                $newStatusEnum = $newStatusNum ? CIBlockPropertyEnum::GetByID($newStatusNum) : false;
                $newStatusText = $newStatusEnum ? $newStatusEnum['VALUE'] : '';
                sendProjectStatusChangeEmails($arFields, $oldStatusText, $newStatusText);
            }
        }
    }
}
    function OnAfterIBlockElementUpdate(&$arFields)
    {
        if (in_array($arFields['IBLOCK_ID'], array(1)) && $arFields['RESULT']) {
                $resNew = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array("sort" => "asc"), array("CODE" => "PR_STATUS"));
                while ($ob = $resNew->GetNext()) {
                    $newStatusValue = $ob['VALUE_ENUM'];
                }
                    $notificationName = '';
                    if ($newStatusValue === 'Завершён') {
                            CIBlockElement::SetElementSection($arFields['ID'], [2]);
                    }
                }
    }

    function OnAfterIBlockElementAdd(&$arFields)
    {
        if (in_array($arFields['IBLOCK_ID'], array(1))) {
            $notificationName = $arFields["NAME"] . " отправлен на модерацию. После проверки данных вам придёт уведомление о создании заявки";
            addNotification($notificationName, $arFields);
        }
    }
