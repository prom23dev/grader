<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;

Loader::includeModule('sale');

$orderId = (int)($_GET['orderId'] ?? 0);
$paymentId = (int)($_GET['paymentId'] ?? 0);

if ($orderId <= 0 || $paymentId <= 0) {
    die('Заказ не найден');
}

$order = Order::load($orderId);
if (!$order) {
    die('Заказ не найден');
}

$payment = null;
foreach ($order->getPaymentCollection() as $orderPayment) {
    if ((int)$orderPayment->getId() === $paymentId) {
        $payment = $orderPayment;
        break;
    }
}

if (!$payment) {
    die('Платёж не найден');
}

if ($payment->isPaid()) {
    LocalRedirect('/projects/');
}

$service = $payment->getPaySystem();
if (!$service) {
    die('Платёжная система не найдена');
}

// initiatePay на стороне хендлера Т-Банка сам обращается к Init API
// и делает LocalRedirect на полученный PaymentURL (см. tinkoff/template/template.php)
$result = $service->initiatePay($payment, \Bitrix\Main\Context::getCurrent()->getRequest());

if (!$result->isSuccess()) {
    die('Ошибка банка: ' . implode('; ', $result->getErrorMessages()));
}

echo $result->getTemplate();
