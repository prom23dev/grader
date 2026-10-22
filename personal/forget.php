<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.auth.forgotpasswd",
    "forgotpasswd",
    Array(
        "AUTH_AUTH_URL" => "",
        "AUTH_REGISTER_URL" => ""
    )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
