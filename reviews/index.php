<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCSS("/reviews/style.css");

//$APPLICATION->SetTitle("");?><!----><?//$APPLICATION->IncludeComponent(
//	"bitrix:news.line",
//	"reviews",
//	Array(
//		"ACTIVE_DATE_FORMAT" => "d.m.Y",
//		"CACHE_GROUPS" => "Y",
//		"CACHE_TIME" => "100",
//		"CACHE_TYPE" => "A",
//		"DETAIL_URL" => "",
//		"FIELD_CODE" => array("NAME","DETAIL_TEXT","DATE_CREATE",""),
//		"IBLOCKS" => array("3"),
//		"IBLOCK_TYPE" => "company",
//		"NEWS_COUNT" => "20",
//		"SORT_BY1" => "ACTIVE_FROM",
//		"SORT_BY2" => "SORT",
//		"SORT_ORDER1" => "DESC",
//		"SORT_ORDER2" => "ASC"
//	)
//);
    $APPLICATION->IncludeComponent("bitrix:news.list", "reviews", array(
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
    "FIELD_CODE" => array("NAME","DETAIL_TEXT","DATE_CREATE",""),
    "PROPERTY_CODE" => array(""),
    "IBLOCK_ID" => REVIEWS_IBLOCK_ID,    // Код информационного блока
    "IBLOCK_TYPE" => "services",    // Тип информационного блока (используется только для проверки)
    "NEWS_COUNT" => "5",    // Количество новостей на странице
    "SORT_BY1" => "ACTIVE_FROM",    // Поле для первой сортировки новостей
    "SORT_ORDER1" => "DESC",    // Направление для первой сортировки новостей
    "FILTER_NAME" => "userNotifications",	// Фильтр
    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
    "PAGER_TEMPLATE" => "pagenav",
    ),
    false
    );?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>