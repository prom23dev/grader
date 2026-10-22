<?php

global $MESS;

$MESS["SALE_TBANK_TITLE"]="T-Bank";
$MESS['SALE_TBANK_DESCRIPTION'] = 'https://www.tbank.ru/kassa/';

$MESS["SALE_TBANK_TERMINAL_ID_NAME"]="Terminal ID";
$MESS["SALE_TBANK_TERMINAL_ID_DESCR"]="Terminal ID is available in the Personal Account https://www.tbank.ru/kassa/.";

$MESS["SALE_TBANK_SHOP_SECRET_WORD_NAME"]="Password";
$MESS["SALE_TBANK_SHOP_SECRET_WORD_DESCR"]="Password is available in the Personal Account https://www.tbank.ru/kassa/.";

$MESS["SALE_TBANK_ORDER_ID_NAME"]="Order ID";
$MESS["SALE_TBANK_ORDER_ID_DESCR"]="The unique order identifier in the online shop system. Select a value \"Order Number\".";

$MESS["SALE_TBANK_SHOULD_PAY_NAME"]="Amount";
$MESS["SALE_TBANK_SHOULD_PAY_DESCR"]="Payment amount.";

$MESS['SALE_TBANK_TAXATION_NAME'] = 'Taxation';
$MESS['SALE_TBANK_TAXATION_DESCR'] = 'System of taxation of your store';
$MESS['SALE_TBANK_TAXATION_OSN'] = 'General system of taxation(osn)';
$MESS['SALE_TBANK_TAXATION_USN_IMCOME'] = 'Simplified taxation system(usn_income)';
$MESS['SALE_TBANK_TAXATION_USN_IMCOME_OUTCOME'] = 'Simplified taxation system(usn_income_outcome)';
$MESS['SALE_TBANK_TAXATION_ESN'] = 'Unified agricultural tax(esn)';
$MESS['SALE_TBANK_TAXATION_PATENT'] = 'Patent system of taxation(patent)';

$MESS['SALE_TBANK_LANGUAGE_NAME'] = 'Payment form language';
$MESS['SALE_TBANK_LANGUAGE_DESCR'] = 'Payment form language of your store';
$MESS['SALE_TBANK_LANGUAGE_RU'] = 'Russian';
$MESS['SALE_TBANK_LANGUAGE_EN'] = 'English';

$MESS['SALE_TBANK_ENABLE_TAXATION_NAME'] = 'Send data to a cheque';
$MESS['SALE_TBANK_ENABLE_TAXATION_DESCR'] = 'Send data to a cheque for payment system';
$MESS['SALE_TBANK_YES'] = 'Yes';
$MESS['SALE_TBANK_NO'] = 'No';

$MESS["SALE_TBANK_FFD"] = "FFD 1.2 version";
$MESS["SALE_TBANK_FFD_DESC"] = "Only if your checkout requires a receipt of FFD version 1.2";

$MESS['SALE_TBANK_DELIVERY_TAXATION_NAME'] = 'Delivery vat rate';
$MESS['SALE_TBANK_DELIVERY_TAXATION_DESCR'] = 'Data will be sent to the payment system';
$MESS['SALE_TBANK_VAT_NONE'] = 'None';
$MESS['SALE_TBANK_VAT_ZERO'] = 'Zero rate';
$MESS['SALE_TBANK_VAT_FIVE'] = 'Five percent rate';
$MESS['SALE_TBANK_VAT_SEVEN'] = 'Seven percent rate';
$MESS['SALE_TBANK_VAT_REDUCED'] = 'Reduced rate';
$MESS['SALE_TBANK_VAT_STANDARD'] = 'Standard rate';

$MESS['SALE_TBANK_PAYBUTTON_NAME'] = 'Pay';

$MESS['SALE_TBANK_UNAVAILABLE'] = 'Payment service is not available. Please try again later. Accept our apologies.';

$MESS['SALE_TBANK_EMAIL_NAME'] = 'User email';
$MESS['SALE_TBANK_EMAIL_DESCR'] = 'Email in order details';

$MESS['SALE_TBANK_ORDER_INFO'] = "Orders information";
$MESS['SALE_TBANK_ORDER_PAYMENT'] = "Order payment";

$MESS['SALE_TBANK_SUCCESS'] = "successful";
$MESS['SALE_TBANK_FAIL'] = "failed";
$MESS['SALE_TBANK_FAIL_TEXT'] = "Order with number %s not found";
$MESS['SALE_TBANK_SUCCESS_TEXT'] = "Order with number %s payment %s <br/> Order state may be see on <a href=\"%s\">orders page</a>";

//
$MESS['SALE_TBANK_CONNECT_ERROR'] = "Unable to connect to the paying service.";
$MESS['SALE_TBANK_SUM_ERROR'] = 'Amount does not converge.';
$MESS['SALE_TBANK_TOKEN_ERROR'] = 'Tokens does not converge.';
$MESS['SALE_TBANK_STATUS_ERROR'] = 'Order status is not defined. To request a status call the method getStatus';
$MESS['SALE_TBANK_QUERY_ERROR'] = 'Failed to send query';
$MESS['SALE_TBANK_PAYMENT_CANCELED'] = 'orders payment canceled';
$MESS['SALE_TBANK_PAYMENT_ID_ERROR'] = 'Payment already made.';

$MESS['SALE_TBANK_FIO_NAME'] = 'Name of user';
$MESS['SALE_TBANK_FIO_DESCR'] = '';
$MESS['SALE_TBANK_PHONE_NAME'] = 'Phone of user';
$MESS['SALE_TBANK_PHONE_DESCR'] = '';

$MESS['SALE_TBANK_TAX_ERROR'] = 'Failed to getting product tax. Please, check you settings.';
$MESS['SALE_TBANK_TAXATION_ERROR'] = 'Failed to getting taxation. Please, check you settings.';
$MESS['SALE_TBANK_TAX_DELIVERY_ERROR'] = 'Failed to getting delivery tax. Please, check you settings.';

$MESS["SALE_TBANK_PAYMENT_METHOD_NAME"] = "Sign of the method of calculation";
$MESS["SALE_TBANK_PAYMENT_METHOD_FULL_PREPAYMENT"] = "Prepayment 100%";
$MESS["SALE_TBANK_PAYMENT_METHOD_PREPAYMENT"] = "Prepayment";
$MESS["SALE_TBANK_PAYMENT_METHOD_ADVANCE"] = "Prepaid expense";
$MESS["SALE_TBANK_PAYMENT_METHOD_FULL_PAYMENT"] = "Full settlement";
$MESS["SALE_TBANK_PAYMENT_METHOD_PARTIAL_PAYMENT"] = "Partial settlement and credit";
$MESS["SALE_TBANK_PAYMENT_METHOD_CREDIT"] = "Credit transfer";
$MESS["SALE_TBANK_PAYMENT_METHOD_CREDIT_PAYMENT"] = "Loan payment";

$MESS['SALE_TBANK_PAYMENT_OBJECT_NAME'] = 'Sign of the subject of calculation';
$MESS['SALE_TBANK_PAYMENT_METHOD_COMMODITY'] = 'Product';
$MESS['SALE_TBANK_PAYMENT_METHOD_EXCISE'] = 'Excisable goods';
$MESS['SALE_TBANK_PAYMENT_METHOD_JOB'] = 'Job';
$MESS['SALE_TBANK_PAYMENT_METHOD_SERVICE'] = 'Service';
$MESS['SALE_TBANK_PAYMENT_METHOD_GAMBLING_BET'] = 'Gambling rate';
$MESS['SALE_TBANK_PAYMENT_METHOD_GAMBLING_PRIZE'] = 'Gamble win';
$MESS['SALE_TBANK_PAYMENT_METHOD_LOTTERY'] = 'Lottery ticket';
$MESS['SALE_TBANK_PAYMENT_METHOD_LOTTERY_PRIZE'] = 'Lottery winnings';
$MESS['SALE_TBANK_PAYMENT_METHOD_INTELLECTUAL_ACTIVITY'] = 'Providing results of intellectual activity';
$MESS['SALE_TBANK_PAYMENT_METHOD_PAYMENT'] = 'Payment';
$MESS['SALE_TBANK_PAYMENT_METHOD_AGENT_COMMISSION'] = 'Agents commission';
$MESS['SALE_TBANK_PAYMENT_METHOD_PARTIAL_COMPOSITE'] = 'Composite subject matter';
$MESS['SALE_TBANK_PAYMENT_METHOD_ANOTHER'] = 'Other subject matter';

$MESS["SALE_TBANK_EMAIL_COMPANY_NAME"] = "Email company";
