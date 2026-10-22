<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
global $USER;

$oldPassword = $_REQUEST['oldPassword'];
$newPassword = $_REQUEST['newPassword'];
$agreeNewPassword = $_REQUEST['agreePassword'];
$oldPasssword = $USER->Login($USER->GetLogin(), $oldPassword, "N");
$fields = array(
    "PASSWORD" => $newPassword,//пароль
    "CONFIRM_PASSWORD" => $agreeNewPassword //подтверждение пароля
);

if (!$oldPasssword['TYPE']) {
    $res = $USER->Update($USER->GetID(), $fields);
    echo true;
} else {
    echo false;
}
?>