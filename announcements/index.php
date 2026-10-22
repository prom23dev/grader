<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Объявления");

// Список фильтруется по гео-привязке текущего посетителя (та же логика,
// что в headerAnnounce.php — см. buildAnnounceGeoDbFilter()). Если для его
// региона/района/города нет ни одного объявления, фильтр снимается и
// показываются все — так список никогда не остаётся пустым без причины.
// Деталь объявления по прямой ссылке всегда открывается независимо от
// гео-привязки (фильтр влияет только на список).
global $announcementsFilter;
$announcementsFilter = buildAnnounceGeoDbFilter();
if ($announcementsFilter && !hasActiveAnnouncements($announcementsFilter)) {
    $announcementsFilter = [];
}
if (isset($_GET['_debug'])) {
    echo '<pre style="position:fixed;top:0;left:0;background:#fff;z-index:99999;max-height:80vh;overflow:auto">';
    echo 'GLOBALS has key: ' . (array_key_exists('announcementsFilter', $GLOBALS) ? 'YES' : 'NO') . "\n";
    print_r($GLOBALS['announcementsFilter'] ?? null);
    echo '</pre>';
}
?>
<?php
$APPLICATION->IncludeComponent(
    "bitrix:news",
    "announcements",
    array(
        "IBLOCK_TYPE" => "services",
        "IBLOCK_ID" => getHeaderAnnounceIblockId(),
        "NEWS_COUNT" => "10",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC",
        "LIST_FIELD_CODE" => array("NAME", "ACTIVE_FROM"),
        "LIST_PROPERTY_CODE" => array(),
        "DETAIL_FIELD_CODE" => array("NAME", "ACTIVE_FROM", "DETAIL_TEXT"),
        "DETAIL_PROPERTY_CODE" => array(),
        "FILTER_NAME" => "announcementsFilter",
        "CHECK_DATES" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DETAIL_DISPLAY_TOP_PAGER" => "N",
        "DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "N",
        "LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
        "PAGER_TEMPLATE" => "pagenav",
        "PAGER_TITLE" => "Объявления",
        "DETAIL_PAGER_TITLE" => "Объявления",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_ELEMENT_CHAIN" => "N",
        "SET_TITLE" => "N",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "CHECK_PERMISSIONS" => "N",
        "USE_PERMISSIONS" => "N",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "3600",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/announcements/",
        "SEF_URL_TEMPLATES" => array(
            "news" => "",
            "detail" => "#ELEMENT_ID#/",
            "section" => "",
        ),
        "VARIABLE_ALIASES" => array(
            "ELEMENT_ID" => "ID",
        ),
    ),
    false
);
?>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
