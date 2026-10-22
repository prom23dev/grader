<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use Bitrix\Sale;
use Bitrix\Sale\Order;
use CSalePaySystemAction;
use TBank\TBankMerchantAPI;

include_once(dirname(__FILE__) . "/sdk/TinkoffMerchantAPI.php");

Loc::loadMessages(__FILE__);

/**
 * Class tinkoffHandler
 * @package Sale\Handlers\PaySystem
 */
class tinkoffHandler extends PaySystem\ServiceHandler implements PaySystem\IRefund
{
    protected $params;

    /**
     * current payment status
     * @var string
     */
    protected $paymentStatus;

    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $params = $this->getParams($payment);
        $initParams = $this->initPayment($params, $payment);
        $paramsExtra = array_merge($params, $initParams);
        $this->setExtraParams($paramsExtra);
        return $this->showTemplate($payment, "template");
    }

    public function getParams(Payment $payment)
    {
        if (!is_array($this->params)) {
            $params = $this->getParamsBusValue($payment);
            foreach ($params as $key => $value)
                $this->params[$key] = trim($value);
        }
        return $this->params;
    }

    public function initPayment($params, $payment)
    {
        $paymentData = $this->getReceipt($params, $payment);

        return $paymentData;
    }

    public function refund(Payment $payment, $refundableSum)
    {
        global $DB;
        $params = $this->getParams($payment);
        $accountNumber = $payment->getField('ACCOUNT_NUMBER');
        $orderNumber = $this->getOrderTbank($payment)->getField("ACCOUNT_NUMBER");
        $result = new PaySystem\ServiceResult();
        $strSql = $DB->Query("SELECT PaymentId FROM tbankRefund WHERE OrderId = '" . $accountNumber . "'");
        $resultSql = $strSql->Fetch();


        if (!$resultSql){
            /* Support old scheme*/
            $strSql = $DB->Query("SELECT PaymentId FROM tbankRefund WHERE OrderId = '" . $orderNumber . "'");
            $resultSql = $strSql->Fetch();
            if (!$resultSql){
                $result->addError(new Error('Error on try to cancel payment.'));
                return $result;
            }
        }
        $paramsRefund = [
            'TerminalKey' => $params['TERMINAL_ID'],
            'PaymentId'   => $resultSql['PaymentId'],
        ];

        $TBankGetState = new TBankMerchantAPI($params['TERMINAL_ID'], $params['SHOP_SECRET_WORD']);
        $requestGetState = $TBankGetState->buildQuery('GetState', $paramsRefund);
        $requestGetState = json_decode($requestGetState, true);
        if (in_array($requestGetState['Status'], ['CONFIRMED', 'AUTHORIZED'], true)) {
            $TBankCancel = new TBankMerchantAPI($params['TERMINAL_ID'], $params['SHOP_SECRET_WORD']);
            $requestCancel = $TBankCancel->buildQuery('Cancel', $paramsRefund);
            $requestCancel = json_decode($requestCancel, true);
            if (in_array($requestCancel['Status'], ['REFUNDED', 'REVERSED'], true)) {
                $result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
            } else {
                $result->addError(new Error($this->convertError($requestCancel['Message']) . ' ' . $this->convertError($requestCancel['Details'])));
            }
        } else {
            $result->addError(new Error('Error on try to confirm payment.'));
        }
        return $result;
    }

    public function convertError($error)
    {
        return mb_convert_encoding($error, LANG_CHARSET);
    }

    public function getReceipt($params, $payment)
    {
        $checkFfd = $params['FFD']; // значение по ФФД 1.2
        $isShipping = false;
        $items = [];
        $errorMessage = '';
        $paymentValues = $payment->getFieldValues();
        $order = $this->getOrderTBank($payment);

        $allPayments = \Bitrix\Sale\Payment::loadForOrder($paymentValues["ORDER_ID"]);
        if (count($allPayments) > 1) {
            $paymentMethod = "prepayment";
        } else {
            $paymentMethod = trim($params['PAYMENT_METHOD']);
        }
        $accountNumber = $paymentValues['ACCOUNT_NUMBER'];
        $propertyCollection = $order->getPropertyCollection();
        $dateOrder = $this->getPhoneEmail($propertyCollection);

        if ($propPayerName = $propertyCollection->getPayerName())
            $payerName = $this->convertEncodingTBank($propPayerName->getValue());

        if ($propPayerPhone = $propertyCollection->getPhone())
            $payerPhone = $this->convertEncodingTBank($propPayerPhone->getValue());

        $amount = round($paymentValues["SUM"] * 100);

        $paymentData = [
            'OrderId' => $accountNumber,
            'Amount'  => $amount,
            'DATA'    => ['Email' => $dateOrder['EMAIL'], 'Name' => $payerName, 'Phone' => $payerPhone, 'Connection_type' => 'Bitrix_2.6'],
        ];

        // Т‑Банк принимает СБП через тот же Init API: для этого передаём PayType=SBP.
        // Для карты поле не задаём, чтобы сохранить стандартное поведение терминала.
        if (isset($_REQUEST['payMethod']) && strtolower((string)$_REQUEST['payMethod']) === 'sbp') {
            $paymentData['PayType'] = 'SBP';
        }

        $basketItems = $order->getBasket()->getBasketItems();
        $isTaxationEnabled = $params['ENABLE_TAXATION'];

        if ($isTaxationEnabled == 1) {

            foreach ($basketItems as $basketItem) {
                $productData = $basketItem->getFieldValues();
                $vatData = $productData['VAT_RATE'];
                \Bitrix\Main\Loader::includeModule('catalog');
                $itemVatID = \CCatalogProduct::getByID($productData['PRODUCT_ID']);

                if (!$itemVatID['VAT_ID']) {
                    $vat = 'none';
                } else {
                    $vat = $this->getTBankTax((int)round($vatData * 100));
                }

                $nameProduct = $this->convertEncodingTBank($productData['NAME']);
                $items[] = [
                    "Name"          => mb_substr($nameProduct, 0, 64, "UTF-8"),
                    "Price"         => round($productData['PRICE'] * 100),
                    "Quantity"      => round($productData['QUANTITY'], 3, PHP_ROUND_HALF_UP),
                    "Amount"        => round($productData['PRICE'] * $productData['QUANTITY'] * 100),
                    "PaymentMethod" => trim($params['PAYMENT_METHOD']),
                    "PaymentObject" => trim($params['PAYMENT_OBJECT']),
                    "Tax"           => $vat,
                ];

                if($checkFfd == 'Y') {
                  foreach ($items as $key => $item) {
                    $items[$key]['MeasurementUnit'] = "pc";
                  }
                }
            }

            $deliveryPrice = $order->getDeliveryPrice();
            if ($deliveryPrice > 0) {
                $deliveryVat = $params['DELIVERY_TAXATION'];
                if (!$deliveryVat) {
                    $errorMessage .= GetMessage("SALE_TBANK_TAX_DELIVERY_ERROR");
                }
                $deliverySystemId = reset($order->getDeliverySystemId());
                $dataDelivery = \Bitrix\Sale\Delivery\Services\Table::getRowById($deliverySystemId);
                $items[] = [
                    "Name"          => mb_substr($this->convertEncodingTBank($dataDelivery['NAME']), 0, 64,  "UTF-8"),
                    "Price"         => round($deliveryPrice * 100),
                    "Quantity"      => 1,
                    "Amount"        => round($deliveryPrice * 100),
                    "PaymentMethod" => $paymentMethod,
                    "PaymentObject" => 'service',
                    "Tax"           => trim($params['DELIVERY_TAXATION']),
                ];

                if($checkFfd == 'Y') {
                  foreach ($items as $key => $item) {
                    $items[$key]['MeasurementUnit'] = "pc";
                  }
                }
                $isShipping = true;
            }

            $taxation = $params['TAXATION'];

            if (!$taxation) {
                $errorMessage .= GetMessage('SALE_TBANK_TAXATION_ERROR');
            }

            $items = $this->balanceAmount($isShipping, $items, $amount);
            $emailCompany = mb_substr($params['EMAIL_COMPANY'], 0, 64, "UTF-8") or 'none';
            $paymentData['Receipt'] = [
                'EmailCompany' => $emailCompany,
                'Email'        => $dateOrder['EMAIL'],
                'Phone'        => $payerPhone,
                'Taxation'     => $taxation,
                'Items'        => $items,
            ];

            if($checkFfd == 'Y') {
                $paymentData['Receipt']['FfdVersion'] = "1.2";
            }
        }

        if ($params['LANGUAGE_PAYMENT'] == 'en') {
            $paymentData['Language'] = 'en';
        }

        $bankHandler = new TBankMerchantAPI($params['TERMINAL_ID'], $params['SHOP_SECRET_WORD']);
        $request = $bankHandler->buildQuery('Init', $paymentData);
        $request = json_decode($request);

        $dataRequest = [
            'error'   => $errorMessage,
            'request' => $request
        ];

        return $dataRequest;
    }

    private function getTBankTax($tax = null)
    {
        $arrayTax = [
            0  => 'vat0',
            5  => 'vat5',
            7  => 'vat7',
            10 => 'vat10',
            20 => 'vat20',
        ];

        if (isset($arrayTax[$tax])) {
            return $arrayTax[$tax];
        } else {
            return 'none';
        }
    }

    public static function getPhoneEmail($propertyCollection)
    {
        $result = [];
        $propsResult = [
            'PHONE' => [],
            'EMAIL' => []
        ];

        foreach ($propertyCollection as $orderProperty) {
            $props = $orderProperty->getProperty();

            if ($props['IS_PHONE'] == 'Y')
                $propsResult['PHONE'][] = $orderProperty->getValue();

            if ($props['IS_EMAIL'] == 'Y')
                $propsResult['EMAIL'][] = $orderProperty->getValue();
        }

        $result['PHONE'] = implode(', ', $propsResult['PHONE']);
        $result['EMAIL'] = implode(', ', $propsResult['EMAIL']);

        return $result;
    }

    private function logsTBank($paymentData, $request, $file)
    {
        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= json_encode($paymentData, JSON_UNESCAPED_UNICODE);
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . $file, $log, FILE_APPEND);

        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= $request;
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . $file, $log, FILE_APPEND);
    }

    private function balanceAmount($isShipping, $items, $amount)
    {
        $itemsWithoutShipping = $items;

        if ($isShipping) {
            $shipping = array_pop($itemsWithoutShipping);
        }

        $sum = 0;

        foreach ($itemsWithoutShipping as $item) {
            $sum += $item['Amount'];
        }

        if (isset($shipping)) {
            $sum += $shipping['Amount'];
        }

        if ($sum != $amount) {
            $sumAmountNew = 0;
            $difference = $amount - $sum;
            $amountNews = [];

            foreach ($itemsWithoutShipping as $key => $item) {
                $itemsAmountNew = $item['Amount'] + floor($difference * $item['Amount'] / $sum);
                $amountNews[$key] = $itemsAmountNew;
                $sumAmountNew += $itemsAmountNew;
            }

            if (isset($shipping)) {
                $sumAmountNew += $shipping['Amount'];
            }

            if ($sumAmountNew != $amount) {
                $max_key = array_keys($amountNews, max($amountNews))[0];    // ключ макс значения
                $amountNews[$max_key] = max($amountNews) + ($amount - $sumAmountNew);
            }

            foreach ($amountNews as $key => $item) {
                $items[$key]['Amount'] = $amountNews[$key];
            }
        }
        return $items;
    }

    public function convertEncodingTBank($productName)
    {
        return mb_convert_encoding($productName, "UTF-8", LANG_CHARSET);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        return $request->get('orderNumber');
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     */
    public function processRequest(Payment $payment, Request $request)
    {
        die('process request');
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return array('RUB');
    }

    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function getOrderTBank($payment)
    {
        $collection = $payment->getCollection();
        $order = $collection->getOrder();
        return $order;
    }
}
