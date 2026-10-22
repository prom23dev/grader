<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
global $USER;
if ($_REQUEST) {
    $reviewText = $_REQUEST["TEXT"];

    $review = new CIBlockElement;
    if ($USER->GetID()) {
        $arLoadReviewsArray = Array(
            "MODIFIED_BY"    => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID"      => REVIEWS_IBLOCK_ID,
            "NAME"           => $USER->GetFullName(),
            "ACTIVE"         => "N",
            "DETAIL_TEXT"   => $reviewText );
        if($reviewId = $review->Add($arLoadReviewsArray))
            echo "success";

    } else {
        echo "fail";
    }
}
