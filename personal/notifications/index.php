<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER;
if (!$USER->IsAuthorized()) {
    header("Location:/");
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
    die();
}
//$APPLICATION->SetTitle("Уведомления");
global $userNotifications;
$userNotifications = array('PROPERTY_USER_ID'=> $USER->GetId());
$APPLICATION->IncludeComponent("bitrix:news.list", "notifications", array(
    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
    "CACHE_GROUPS" => "Y",    // Учитывать права доступа
    "CACHE_TIME" => "3600",    // Время кеширования (сек.)
    "CACHE_TYPE" => "A",    // Тип кеширования
    "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
    "DISPLAY_BOTTOM_PAGER" => "Y",    // Выводить под списком
    "DISPLAY_DATE" => "Y",    // Выводить дату элемента
    "DISPLAY_NAME" => "Y",    // Выводить название элемента
    "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
    "DISPLAY_PREVIEW_TEXT" => "Y",    // Выводить текст анонса
    "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
    "AJAX_OPTION_STYLE" => "Y",    // Включить подгрузку стилей
    "FIELD_CODE" => array("PREVIEW_PICTURE"),
    "PROPERTY_CODE" => array("USER_ID", "VIEWED"),
    "IBLOCK_ID" => NOTIFICATION_IBLOCK_ID,    // Код информационного блока
    "IBLOCK_TYPE" => "services",    // Тип информационного блока (используется только для проверки)
    "NEWS_COUNT" => "10",    // Количество новостей на странице
    "SORT_BY1" => "TIMESTAMP_X",    // Поле для первой сортировки новостей
    "SORT_ORDER1" => "DESC",    // Направление для первой сортировки новостей
    "FILTER_NAME" => "userNotifications",	// Фильтр
    "INCLUDE_IBLOCK_INTO_CHAIN" => "N"
),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>