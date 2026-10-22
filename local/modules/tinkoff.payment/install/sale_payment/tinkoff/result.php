<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;

Loc::loadMessages(__FILE__);
Loader::includeModule('sale');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");


$APPLICATION->SetPageProperty("title", GetMessage("SALE_TBANK_ORDER_INFO"));
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle(GetMessage("SALE_TBANK_ORDER_PAYMENT"));

function find_order_result($orderId)
{
    $paymentIndex = strripos($orderId, "/");
    if ($paymentIndex)
        $accountNumber = mb_substr($orderId, 0, $paymentIndex);
	else 
		$accountNumber = $orderId;
    $order = Order::loadByAccountNumber($accountNumber);
    return $order;
}

$order = find_order_result($_REQUEST['OrderId']);
if (!$order)
    echo "Order not found";
else {
	$accountNumber = $order->getField("ACCOUNT_NUMBER");
    $status = $_REQUEST['Success'] == 'true' ? GetMessage("SALE_TBANK_SUCCESS") : GetMessage("SALE_TBANK_FAIL");
    $statusPageURL = sprintf('%s/%s',GetPagePath('personal/orders'), $accountNumber);
    echo sprintf(GetMessage("SALE_TBANK_SUCCESS_TEXT"), $accountNumber, $status, $statusPageURL);
}
