<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
\Bitrix\Main\Loader::includeModule('sale');
CModule::IncludeModule('iblock');

    $arOrder = Array("ID" => "DESC"); $arSelect = Array("ID", "PROPERTY_PR_PHOTO");$arFilter = Array("IBLOCK_ID"=>"1", "ID"=>54);
    $res = CIBlockElement::GetList($arOrder, $arFilter, false, Array("nPageSize"=>1), $arSelect);

$ob = $res->GetNextElement();
if ($ob) {

    $arFields = $ob->GetFields();

    $imgResized = resizeImage($arFields['PROPERTY_PR_PHOTO_VALUE'], 500, 360);
    var_dump($imgResized);

    $el = new CIBlockElement;
        $PROP = array("PR_PHOTO" => CFile::MakeFileArray($imgResized),"PR_ADDRESS"=>"ул. Гагарина, д.95", "PR_COST"=>"1 000 000 RUB", "PR_LOCATION"=>"Волгоград", "PR_WORK_TYPE"=>2);

        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "IBLOCK_SECTION" => 1,          // элемент лежит в корне раздела
            "PROPERTY_VALUES"=> $PROP,
            "PREVIEW_TEXT"   => "текст для списка элементов",
        );
        $PRODUCT_ID = 54;  // изменяем элемент с кодом (ID) 2
        $res = $el->Update($PRODUCT_ID, $arLoadProductArray);
    }


