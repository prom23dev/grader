<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */

foreach ($arResult['PROPERTIES']['PR_USER_DATA']['VALUE'] as $userId) {
    $rsUser = CUser::GetByID($userId);
    $arUser = $rsUser->Fetch();
    $arResult['USER_INFO'][] = array('LOGIN' => $arUser['LOGIN']);
}

// История переводов средств по проекту: стандартные оплаченные заказы Sale,
// привязанные к проекту служебным свойством заказа PROJECT_ID (см. local/php_interface/projectDonations.php)
$arResult['DONATIONS'] = getProjectDonations((int)$arResult['ID']);
foreach ($arResult['DONATIONS'] as $donation) {
    $arResult['PAYS'][] = array(
        'LOGIN' => $donation['LOGIN'] ?: 'Аноним',
        'AMOUNT' => $donation['AMOUNT'],
        'DATE_PAID' => $donation['DATE_PAID'],
    );
}

// "Собрано" считается из реально оплаченных пожертвований, а не из
// статичного свойства PR_ITOG.
$arResult['COLLECTED_AMOUNT'] = getProjectCollectedAmount((int)$arResult['ID']);

// PR_LOCATION/PR_LOCATION_DISTRICT/PR_LOCATION_SETTLEMENT хранят ID
// элементов HL-блоков GeoRegion/GeoDistrict/GeoSettlement — резолвим в
// читаемые названия для вывода (см. local/php_interface/geoLocation.php).
$arResult['LOCATION_REGION_NAME'] = getGeoRegionName($arResult['PROPERTIES']['PR_LOCATION']['VALUE'] ?? '');
$arResult['LOCATION_DISTRICT_NAME'] = getGeoDistrictName($arResult['PROPERTIES']['PR_LOCATION_DISTRICT']['VALUE'] ?? '');
$arResult['LOCATION_SETTLEMENT_NAME'] = getGeoSettlementName($arResult['PROPERTIES']['PR_LOCATION_SETTLEMENT']['VALUE'] ?? '');

// Окно подачи возражений на итоговый отчёт (5 календарных дней с даты публикации отчёта)
$arResult['CAN_OBJECT'] = false;
$arResult['OBJECTION_DEADLINE'] = null;
global $USER;
if ($USER->IsAuthorized()) {
    $reportDateValue = $arResult['PROPERTIES']['PR_REPORT_DATE']['VALUE'] ?? '';
    $arResult['CAN_OBJECT'] = canSubmitObjection((int)$USER->GetID(), (int)$arResult['ID'], $reportDateValue);
    $deadline = getProjectObjectionDeadline($reportDateValue);
    $arResult['OBJECTION_DEADLINE'] = $deadline ? $deadline->format('d.m.Y H:i') : null;
}

use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    YandexCheckout\Client;
Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

$dbRes = \Bitrix\Sale\Order::getList([
    'select' => ['*'],
    'filter' => [
        "USER_ID" => $USER->GetID(), //по пользователю
    ],
    'order' => ['ID' => 'DESC']
]);
while ($order = $dbRes->fetch()){
//    var_dump($order);
}