<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $USER;
if (!$USER->IsAuthorized()) {
    header("Location:/");
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
    die();
}
$APPLICATION->SetTitle("Мои поддержанные проекты");

global $userSupportedProjects;
$projectIds = getUserSupportedProjectIds((int)$USER->GetID());
$userSupportedProjects = ['ID' => $projectIds ?: [0]];
?>
<?$APPLICATION->IncludeComponent("bitrix:news.list", "my-support", Array(
    "ACTIVE_DATE_FORMAT" => "d.m.Y",
    "AJAX_MODE" => "N",
    "CACHE_FILTER" => "N",
    "CACHE_GROUPS" => "Y",
    "CACHE_TIME" => "0",
    "CACHE_TYPE" => "N",
    "CHECK_DATES" => "N",
    "DISPLAY_BOTTOM_PAGER" => "Y",
    "DISPLAY_DATE" => "Y",
    "DISPLAY_NAME" => "Y",
    "DISPLAY_PICTURE" => "N",
    "DISPLAY_PREVIEW_TEXT" => "N",
    "DISPLAY_TOP_PAGER" => "N",
    "FIELD_CODE" => array("ID", "NAME", "DATE_CREATE", ""),
    "FILTER_NAME" => "userSupportedProjects",
    "IBLOCK_ID" => PROJECTS_IBLOCK_ID,
    "IBLOCK_TYPE" => "company",
    "INCLUDE_SUBSECTIONS" => "Y",
    "NEWS_COUNT" => "20",
    "PAGER_TEMPLATE" => "pagenav",
    "PROPERTY_CODE" => array("PR_ADDRESS", "PR_STATUS", "PR_REPORT_DATE"),
    "SET_TITLE" => "N",
    "SORT_BY1" => "ACTIVE_FROM",
    "SORT_BY2" => "SORT",
    "SORT_ORDER1" => "DESC",
    "SORT_ORDER2" => "ASC",
), false);?>
<?require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>
