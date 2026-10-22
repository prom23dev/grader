<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// 1. Сначала получаем данные
$request = json_decode(file_get_contents('php://input'), true);

// Регион/район/населённый пункт приходят как ID элементов HL-блоков
// GeoRegion/GeoDistrict/GeoSettlement (см. map/assets/js/script.js), а
// свойства проекта PR_LOCATION* хранят эти же ID (см. local/php_interface/geoLocation.php).
$region     = !empty($request['region'])     ? trim($request['region'])     : '';
$district   = !empty($request['district'])   ? trim($request['district'])   : '';
$settlement = !empty($request['settlement']) ? trim($request['settlement']) : '';

// 2. Объявляем глобальный фильтр
global $arPFilter;
$arPFilter = [];

// 3. Формируем фильтр последовательно.
// Если выбраны и район, и НП — в фильтр попадут ОБА, что исключит дубли из других районов.
if ($region && !$district && !$settlement) {
    $arPFilter["PROPERTY_PR_LOCATION"] = $region;
    $arPFilter["=PROPERTY_PR_LOCATION_DISTRICT"] = false;
    $arPFilter["=PROPERTY_PR_LOCATION_SETTLEMENT"] = false;
}
elseif ($region && $district && !$settlement) {
    $arPFilter["PROPERTY_PR_LOCATION"] = $region;
    $arPFilter["PROPERTY_PR_LOCATION_DISTRICT"] = $district;
    $arPFilter["=PROPERTY_PR_LOCATION_SETTLEMENT"] = false;
}

elseif ($region && $district && $settlement) {
    $arPFilter["PROPERTY_PR_LOCATION"] = $region;
    $arPFilter["PROPERTY_PR_LOCATION_DISTRICT"] = $district;
    $arPFilter["PROPERTY_PR_LOCATION_SETTLEMENT"] = $settlement;
}

$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "projects-list",
    array(
        "ACTIVE_DATE_FORMAT" => "j F Y",
        "AJAX_OPTION_JUMP" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "N",
        "DISPLAY_DATE" => "Y",
        "IBLOCK_ID" => "1",
        "PARENT_SECTION_CODE" => "",
        "FILTER_NAME" => "arPFilter",
        "STATUS" => 'ongoing',
        "IBLOCK_TYPE" => "news",
        "FIELD_CODE" => array("NAME"),
        "PROPERTY_CODE" => array(
            "PR_PHOTO",
            "PR_SQUARE",
            "PR_DEADLINES",
            "PR_COST",
            "PR_LOCATION",
            "PR_LOCATION_DISTRICT",
            "PR_LOCATION_SETTLEMENT",
            "PR_REGION"
        ),
        "INCLUDE_SUBSECTIONS" => "Y",
        "NEWS_COUNT" => "10",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "SET_STATUS_404" => "N",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        'CHECK_PERMISSIONS' => 'N',
        'SHOW_HISTORY' => 'Y'
    )
);

// Флашим managed-кеш на диск (см. get-geo.php) — без этого D7-кеш,
// используемый внутри компонента/ядра, пишется только в память процесса
// и теряется, т.к. этот скрипт не подключает epilog_after.php.
\CMain::FinalActions();
