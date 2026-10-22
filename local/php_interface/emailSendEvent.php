<?php
// define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/log/log.txt");
// AddMessage2Log('LOGIN: '.$res['LOGIN'].PHP_EOL, "user");


AddEventHandler('main', 'OnBeforeEventSend', "my_OnBeforeEventSend");

function my_OnBeforeEventSend(&$arFields, $arTemplate)
{
    $password = $_REQUEST['REGISTER']['PASSWORD'];
    $arFields['PASSWORD'] = $password;
}