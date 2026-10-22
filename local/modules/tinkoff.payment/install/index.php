<?php

use Bitrix\Main\Application;
use Bitrix\Main\IO\File;
use Bitrix\Sale;
use Bitrix\Sale\Internals\PaySystemActionTable;
use Bitrix\Sale\PaySystem\Manager;

CModule::IncludeModule("sale");

IncludeModuleLangFile(__FILE__);

class tinkoff_payment extends CModule
{
    const MODULE_ID = 'tinkoff.payment';
    public $MODULE_ID = 'tinkoff.payment';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public $strError = '';

    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("tbank_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("tbank_MODULE_DESC");
        $this->PARTNER_NAME = GetMessage("tbank_MODULE_NAME");
        $this->PARTNER_URI = "https://www.tbank.ru/kassa/";
    }

    public function InstallEvents()
    {
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }

    public function rmFolder($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->rmFolder($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);

        return true;
    }

    public function copyDir($source, $destination)
    {
        if (is_dir($source)) {
            @mkdir($destination, 0755);
            $directory = dir($source);
            while (FALSE !== ($readdirectory = $directory->read())) {
                if ($readdirectory == '.' || $readdirectory == '..') continue;
                $PathDir = $source . '/' . $readdirectory;
                if (is_dir($PathDir)) {
                    $this->copyDir($PathDir, $destination . '/' . $readdirectory);
                    continue;
                }
                copy($PathDir, $destination . '/' . $readdirectory);
            }
            $directory->close();
        } else {
            copy($source, $destination);
        }
    }

    public function InstallFiles($arParams = array())
    {
        if (!is_dir($ipn_dir = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/')) {
            mkdir($ipn_dir, 0755);
        }
        if (is_dir($source = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install')) {
            // bitrix/modules/sale/payment - системный обработчик
            $this->copyDir($source . "/handler", $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/payment');
            // php_interface/include/sale_payment - пользовательский обработчик
            $this->copyDir($source . "/handler", $ipn_dir);

            if (!is_dir($personal_dir = $_SERVER['DOCUMENT_ROOT'] . '/personal/')) {
                mkdir($personal_dir, 0755);
            }
            if (!is_dir($order_dir = $_SERVER['DOCUMENT_ROOT'] . '/personal/order/')) {
                mkdir($order_dir, 0755);
            }
            copy($source . "/notifications/notification.php", $order_dir . 'notification.php');
            copy($source . "/notifications/success.php", $order_dir . 'success.php');
            copy($source . "/notifications/failed.php", $order_dir . 'failed.php');
        }

        $this->addModuleLogo();

        return true;
    }

    public function UnInstallFiles()
    {
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/sale/payment/tinkoff.payment');
        $this->rmFolder($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/tinkoff');
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;
        global $DB;

        $strSql = "
        CREATE TABLE IF NOT EXISTS tbankRefund (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, OrderId VARCHAR(20), PaymentId VARCHAR(20) UNIQUE, DataCreated timestamp default now() on update now())
        ";
        $result = $DB->Query($strSql);

        $this->InstallFiles();

        RegisterModule(self::MODULE_ID);
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        UnRegisterModule(self::MODULE_ID);

        $this->UnInstallFiles();
    }


    public function addModuleLogo() {
        
        $lang = LANGUAGE_ID;
        
        $paySystemSettings = array(
            "NAME" => $this->MODULE_NAME,
            "DESCRIPTION" => "",  
            "PSA_NAME" => $lang == 'en' ? "T-Bank" : "Т-Банк",
            "ACTION_FILE" => "tinkoff", 
            "ACTIVE" => "N",
            "PAY_SYSTEM_ID" => "",
            "ENTITY_REGISTRY_TYPE" => "ORDER",
            "IS_CASH" => "A",
            "NEW_WINDOW" => "N",
            "HAVE_PREPAY" => "N",
            "HAVE_RESULT" => "N",
            "HAVE_ACTION" => "N",
            "HAVE_PAYMENT" => "Y",
            "HAVE_RESULT_RECEIVE" => "Y",
            "ENCODING" => "utf-8",
            "SORT" => 100,
        );
        
        $imagePath = Application::getDocumentRoot() . '/bitrix/modules/tinkoff.payment/install/sale_payment/tinkoff/';

        if($lang == 'en') {
            $imagePath = $imagePath . 'logo-en.png';
        } else {
            $imagePath = $imagePath . 'logo.png';
        }

        if (File::isFileExists($imagePath)) {
            $paySystemSettings['LOGOTIP'] = \CFile::MakeFileArray($imagePath);
            $paySystemSettings['LOGOTIP']['MODULE_ID'] = "sale";
            \CFile::SaveForDB($paySystemSettings, 'LOGOTIP', 'sale/paysystem/logotip');
        }

        $result = PaySystemActionTable::add($paySystemSettings);
        
        if ($result->isSuccess()) {
            $id = $result->getId();
			Sale\PaySystem\Manager::update(
				$id,
				[
					'PARAMS' => serialize(
						[
							'BX_PAY_SYSTEM_ID' => $id,
						]
					),
					'PAY_SYSTEM_ID' => $id,
				]
			);
        }
        
        return 0;
    }

}


