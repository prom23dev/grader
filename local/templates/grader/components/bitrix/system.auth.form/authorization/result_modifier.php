<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CModule::IncludeModule('iblock');
$notificationObj = \Bitrix\Iblock\Elements\ElementNotifyTable::getList([
    'select' => ['ID'],
    'filter' => [
        'ACTIVE' => 'Y',
        'USER_ID.VALUE' => $USER->GetID(),
    ],
]);

$arResult['NOTIFY_COUNT'] = 0;
while ($notificationObj->fetch()) {
    $arResult['NOTIFY_COUNT']++;
}
