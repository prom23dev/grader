<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//перечень ваших полей задаем как нам необходимо по ТЗ
$arResult['FIELDS'] = array(
    'LAST_NAME',
    'NAME',
    'SECOND_NAME',
    'LOGIN',
    'PERSONAL_BIRTHDAY',
    'PERSONAL_GENDER',
    'PERSONAL_PHONE',
    'EMAIL',
);

$arResult['PERSONAL_FIELDS'] = array();
foreach ($arResult['FIELDS'] as $field) {
    foreach($arResult["arUser"] as $userKey => $userField) {
        if ($field === $userKey) {
            if (!empty($userField)){
                $arResult['PERSONAL_FIELDS'][$field] = $userField;
            } else {
                $arResult['PERSONAL_FIELDS'][$field] = '';
            }
        }
    };
}

?>