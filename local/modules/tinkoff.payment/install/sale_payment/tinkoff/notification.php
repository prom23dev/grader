<?php

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Order;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

Loader::includeModule('sale');
Loader::includeModule('HttpRequest');
Loc::loadMessages(__FILE__);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include(dirname(__FILE__) . "/sdk/tinkoff_autoload.php");

/* Find Order && Find Payment*/
function find_order($request)
{
    $paymentIndex = strripos($request->OrderId, "/");
    if (!$paymentIndex){
        return FALSE;
    }
    $accountNumber = substr($request->OrderId, 0, $paymentIndex);
    $order = Order::loadByAccountNumber($accountNumber);
    if (!$order) {
        return FALSE;
    }
    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $payment) {
        $baseSum = $payment->getField("SUM") * 100;
        $baseSum = (int)($baseSum);
        if ($payment->getField('ACCOUNT_NUMBER') == $request->OrderId &&
            $baseSum  == $request->Amount){
            break;
        }
        $payment = NULL;
    }
    if (!$payment) {
        return FALSE;
    }
    return [$order, $payment];
}

/* Support for old scheme */
function find_order_old($request){
    $order = strripos($request->OrderId, "/") ? Order::loadByAccountNumber(stristr($request->OrderId, '/', true)) : Order::load($request->OrderId);
  
    if (!$order){
        die('ORDER NOT FOUND');
    }
    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $payment) {
     
        $ps = $payment->getPaySystem();
        $payStatus = ($request->Status == "REFUNDED") ? "Y" : "N";
        $baseSum = $payment->getField("SUM") * 100;
        $baseSum = (int)($baseSum);
  
        if ($payment->getField("PAID") == $payStatus &&
            $ps->getField("ACTION_FILE") == "tinkoff" &&
            $baseSum == $request->Amount){
                break;
            }
        $payment = NULL;
        }
    if (!$payment) {
        die ("PAYMENT NOT FOUND");
    }
    return [$order, $payment];
}
$request = json_decode(HttpRequest::getInput());
if (!$request->OrderId)
{
    die();
}
[$order, $payment] = find_order($request);  
if (!$order || !$payment)
{
    [$order, $payment] = find_order_old($request);
    if (!$order || !$payment)
        die("Order not found");
}
$extra = $payment->getPaySystem()->getParamsBusValue($payment);	
$notificationModel = new TBankNotification($extra["TERMINAL_ID"], $extra["SHOP_SECRET_WORD"]);

try {
    $notificationModel->checkNotification($request);
} catch (TBankException $e) {
    die($e->getMessage());
}

if ($notificationModel->isOrderFailed()) {
    $payment->setPaid('N');
    $order->save();
} elseif ($notificationModel->isOrderPaid()) {
    $payment->setPaid('Y');
    $order->save();
} elseif ($notificationModel->isOrderRefunded()) {
    $payment->setPaid('N');
    $order->setField('STATUS_ID', 'N');
    $allPayments = \Bitrix\Sale\Payment::loadForOrder($order->getId());
    if (count($allPayments) == 1) 
        $order->setField("CANCELED","Y");
    $order->save();
} else {
    die('OK');  
}

die('OK');