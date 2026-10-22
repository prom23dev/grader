<?php

// Читаем тело запроса ДО подключения ядра Bitrix — прolog уже разбирает php://input,
// и повторное чтение после него возвращает пустую строку.
$rawInput = file_get_contents('php://input');

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Order;

header('Content-Type: application/json');

function respondError($message, $code = 400)
{
    http_response_code($code);
    echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

Loader::includeModule('sale');
Loader::includeModule('iblock');

$input = json_decode($rawInput, true);

$amountRub = isset($input['amount']) ? (int)preg_replace('/\D/', '', (string)$input['amount']) : 0;
if ($amountRub <= 0) {
    respondError('Укажите сумму перевода');
}

$projectId = isset($input['projectId']) ? (int)$input['projectId'] : 0;
if ($projectId <= 0) {
    respondError('Не указан проект');
}

$project = \CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'ID' => $projectId, 'ACTIVE' => 'Y'],
    false,
    false,
    ['ID', 'NAME']
)->Fetch();
if (!$project) {
    respondError('Проект не найден', 404);
}

$name = isset($input['name']) ? trim((string)$input['name']) : '';
$email = isset($input['email']) ? trim((string)$input['email']) : '';
$phone = isset($input['phone']) ? trim((string)$input['phone']) : '';

global $USER;
$userId = $USER->IsAuthorized() ? (int)$USER->GetID() : (int)\CSaleUser::GetAnonymousUserID();

$siteId = 's1';
$personTypeId = getDonationPersonTypeId();
if ($personTypeId <= 0) {
    respondError('Платёжная система не настроена', 500);
}

$order = Order::create($siteId, $userId);
$order->setPersonTypeId($personTypeId);
$order->setField('CURRENCY', 'RUB');

// PRODUCT_NAME хранится в varchar(100), режем по байтам с запасом под кавычки/префикс
$productName = 'Поддержка проекта «' . $project['NAME'] . '»';
while (strlen($productName) > 95) {
    $project['NAME'] = mb_substr($project['NAME'], 0, mb_strlen($project['NAME']) - 1);
    $productName = 'Поддержка проекта «' . $project['NAME'] . '»';
}

$basket = Basket::create($siteId);
$item = $basket->createItem('sale', 0);
$item->setFields([
    'NAME' => $productName,
    'QUANTITY' => 1,
    'CURRENCY' => 'RUB',
    'PRICE' => $amountRub,
    'BASE_PRICE' => $amountRub,
    'CUSTOM_PRICE' => 'Y',
    'TYPE' => BasketItem::TYPE_SERVICE,
]);
$order->setBasket($basket);

$propertyCollection = $order->getPropertyCollection();

$propValues = ['PROJECT_ID' => $projectId];
if ($email !== '') {
    $propValues['EMAIL'] = $email;
}
if ($phone !== '') {
    $propValues['PHONE'] = $phone;
}
if ($name !== '') {
    $propValues['FIO'] = $name;
}

foreach ($propValues as $code => $value) {
    $propId = getDonationOrderPropId($code);
    if ($propId > 0) {
        $propertyItem = $propertyCollection->getItemByOrderPropertyId($propId);
        if ($propertyItem) {
            $propertyItem->setValue($value);
        }
    }
}

$paySystemActionId = getTinkoffPaySystemActionId();
if ($paySystemActionId <= 0) {
    respondError('Платёжная система Т-Банк не настроена', 500);
}

$paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById($paySystemActionId);
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem($paySystemService);
$payment->setFields([
    'SUM' => $amountRub,
    'CURRENCY' => 'RUB',
]);

$saveResult = $order->save();
if (!$saveResult->isSuccess()) {
    respondError('Не удалось создать заказ: ' . implode('; ', $saveResult->getErrorMessages()), 500);
}

$payMethod = isset($input['payMethod']) && strtolower((string)$input['payMethod']) === 'sbp' ? 'sbp' : 'card';
$paymentUrl = '/local/templates/grader/assets/ajax/startTinkoffPayment.php?orderId=' . $order->getId() . '&paymentId=' . $payment->getId();
$paymentUrl .= '&payMethod=' . rawurlencode($payMethod);

echo json_encode(['paymentURL' => $paymentUrl]);
