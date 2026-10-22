<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $USER;
if (!$USER->IsAuthorized()) {
    header("Location:/");
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
    die();
}
?>
<?$APPLICATION->IncludeComponent("bitrix:main.include","",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "inc",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/include/settings.php"
    )
);?>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>