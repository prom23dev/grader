<?php

use TBank\TBankMerchantAPI;

/**
 * Class TBank
 */
class TBankNotification extends TBankMerchantAPI
{
    /**
     * After calling initPayment()
     */
    const STATUS_NEW = 'NEW';

    /**
     * After calling cancelPayment()
     * Not Implemented here
     */
    const STATUS_CANCELED = 'CANCELED';

    /**
     * Intermediate status (transaction is in process)
     */
    const STATUS_PREAUTHORIZING = 'PREAUTHORIZING';

    /**
     * After showing payment form to the customer
     */
    const STATUS_FORMSHOWED = 'FORMSHOWED';

    /**
     * Intermediate status (transaction is in process)
     */
    const STATUS_AUTHORIZING = 'AUTHORIZING';

    /**
     * Intermediate status (transaction is in process)
     * Customer went to 3DS
     */
    const STATUS_THREEDSCHECKING = 'THREEDSCHECKING';

    /**
     * Payment rejected on 3DS
     */
    const STATUS_REJECTED = 'REJECTED';

    /**
     * Payment compete, money holded
     */
    const STATUS_AUTHORIZED = 'AUTHORIZED';

    /**
     * After calling reversePayment
     * Charge money back to customer
     * Not Implemented here
     */
    const STATUS_REVERSING = 'REVERSING';

    /**
     * Money charged back, transaction cmplete
     */
    const STATUS_REVERSED = 'REVERSED';

    /**
     * After calling confirmePayment()
     * Confirm money wright-off
     * Not Implemented here
     */
    const STATUS_CONFIRMING = 'CONFIRMING';

    /**
     * Money written off
     */
    const STATUS_CONFIRMED = 'CONFIRMED';

    /**
     * After calling refundPayment()
     * Retrive money back to customer
     * Not Implemented here
     */
    const STATUS_REFUNDING = 'REFUNDING';

    /**
     * Money is back on the customer account
     */
    const STATUS_REFUNDED = 'REFUNDED';

    const STATUS_UNKNOWN = 'UNKNOWN';

    /**
     * Terminal id, bank give it to you
     * @var int
     */
    private $terminalId;

    /**
     * Secret key, bank give it to you
     * @var string
     */
    private $secret;

    /**
     * Read API documentation
     * @var string
     */
    private $paymentUrl;

    /**
     * Current payment status
     * @var string
     */
    private $paymentStatus;

    /**
     * Payment id in bank system
     * @var int
     */
    private $paymentId;

    /**
     * @param $terminalId int
     * @param $secret string
     * @param $paymentUrl string
     */
    public function __construct($terminalId, $secret)
    {
        parent::__construct($terminalId, $secret);
    }

    /**
     * Recieves notification from TSC, checks is request valid.
     * Should OK in response
     *
     * @param $params
     * @throws TBankException
     */
    public function checkNotification($params)
    {
        global $DB;

        $originalToken = $params->Token;

        if (isset($params->Token)) {
            unset($params->Token);
        }

        $params->Success = $params->Success ? 'true' : 'false';

        $params->Password = htmlspecialchars_decode($this->_secretKey);

        $arrayObjRequest = new ArrayObject($params);
        $arrayObjRequest->ksort();

        $tokenStr = '';

        if(isset($arrayObjRequest["Data"])) {
            unset($arrayObjRequest["Data"]);
        }

        foreach ($arrayObjRequest as $key => $val) {
            $tokenStr .= $val;
        }

        $genToken = $this->getHash($params);

        if ($originalToken != $genToken) {
            throw new TBankException(sprintf(GetMessage("SALE_TBANK_TOKEN_ERROR")));
        }

        $this->isRequestSuccess($params->Success);
        if ($params->Status == self::STATUS_AUTHORIZED) {

            $connection = Bitrix\Main\Application::getConnection();
            $sqlHelper = $connection->getSqlHelper();

            $orderId = $sqlHelper->ForSql($params->OrderId);
            $payId = $sqlHelper->ForSql($params->PaymentId);

            $sql = "SELECT * FROM tbankRefund WHERE PaymentId = '".$payId."'";
            $selectSql = $connection->Query($sql)->Fetch();

            if (empty($selectSql)){
                $strSql = "INSERT INTO tbankRefund (OrderId,PaymentId) values ('".$orderId."', '".$payId."')";
                $result = $DB->Query($strSql, true);
            } else {
                throw new TBankException(sprintf(GetMessage("SALE_TBANK_PAYMENT_ID_ERROR")));
            }
        }

        $this->paymentStatus = $params->Status;
        $this->paymentId = $params->PaymentId;
    }

    /**
     * Check if order is complete and money paid
     *
     * @return bool
     * @throws TBankException
     */
    public function isOrderPaid()
    {   $this->checkStatus();

        return in_array($this->paymentStatus, array(self::STATUS_CONFIRMED));
    }

    public function isOrderAuthorized()
    {
        $this->checkStatus();

        return in_array($this->paymentStatus, array(self::STATUS_AUTHORIZED));
    }

    /**
     * Checks if oreder is failed
     *
     * @return bool
     */
    public function isOrderFailed()
    {
        return in_array($this->paymentStatus, array(self::STATUS_CANCELED, self::STATUS_REVERSED, self::STATUS_REJECTED));
    }

    /**
     * Checks if oreder is refunded
     *
     * @return bool
     */
    public function isOrderRefunded()
    {
        return in_array($this->paymentStatus, array(self::STATUS_REFUNDED));
    }

    /**
     * Check is status variable is set
     *
     * @throws TBankException
     */
    private function checkStatus()
    {
        if (is_null($this->paymentStatus)) {
            throw new TBankException(sprintf(GetMessage("SALE_TBANK_STATUS_ERROR")));
        }
    }

    /**
     * Checks request success
     *
     * @param $success
     * @throws TBankException
     */
    private function isRequestSuccess($success)
    {
        if ($success == false) {
            throw new TBankException(sprintf(GetMessage("SALE_TBANK_QUERY_ERROR")));
        }
    }
}
