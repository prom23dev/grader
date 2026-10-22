<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

if ($_REQUEST) {
    $name = $_REQUEST['name'];
    $phone = $_REQUEST['phone'];
    $email = $_REQUEST['email'];
    $payMethod = $_REQUEST['payMethod'];

var_dump($_REQUEST);
}
//header("Location:/");
