<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
CModule::IncludeModule('iblock');

if ($_REQUEST) {
    $phoneNumber = $_REQUEST['phoneNum'];

    $el = new CIBlockElement;

    $arLoadProductArray = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => CALL_BACK_IBLOCK_ID,
        "NAME" => $phoneNumber,
        "CODE" => CUtil::translit($phoneNumber, "ru"),
        "ACTIVE" => "N",
    );
    if ($PRODUCT_ID = $el->Add($arLoadProductArray))
        echo $PRODUCT_ID;
    else
        echo $el->LAST_ERROR;
}
