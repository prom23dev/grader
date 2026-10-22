<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?
if ($USER->IsAuthorized()) {
    $APPLICATION->SetTitle("Личный кабинет");

    $APPLICATION->IncludeComponent("bitrix:system.auth.form", "lk-personal", Array(
        "FORGOT_PASSWORD_URL" => "/personal/forget.php",	// Страница забытого пароля
        "PROFILE_URL" => "/personal/profile/index.php",	// Страница профиля
        "REGISTER_URL" => "/personal/registration.php",	// Страница регистрации
        "SHOW_ERRORS" => "N",	// Показывать ошибки
    ),
        false
    );
} else {
   header("Location:/");
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
