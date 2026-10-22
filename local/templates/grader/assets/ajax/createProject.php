<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
\Bitrix\Main\Loader::includeModule('sale');
CModule::IncludeModule('iblock');
global $USER;
if ($_REQUEST) {
    $lastId = null;
    $rawLocation = json_decode($_REQUEST['location'],true);
    $regionId = resolveGeoRegionId((string)$rawLocation['region']);
    $districtId = resolveGeoDistrictId((string)$rawLocation['district'], $regionId);
    $settlementId = resolveGeoSettlementId((string)$rawLocation['settlement'], $districtId);
    $region = $regionId ?: '';
    $district = $districtId ?: '';
    $settlement = $settlementId ?: '';
    $street = $_REQUEST['street'];
    $workType = $_REQUEST['work-type'];
    $dopInfo = $_REQUEST['dop-info'];
    $photos = $_FILES['files'];
    $name = $_REQUEST['name'];
    $phone = $_REQUEST['phone'];
    $email = $_REQUEST['email'];
    // Порядковый номер проекта считается отдельно для каждого населённого
    // пункта, поэтому общий ID элемента инфоблока для этого не подходит.
    $settlementProjectCount = 0;
    if ($settlementId > 0) {
        $settlementProjects = CIBlockElement::GetList(
            [],
            [
                "IBLOCK_ID" => PROJECTS_IBLOCK_ID,
                "PROPERTY_PR_LOCATION_SETTLEMENT" => $settlementId,
            ],
            [],
            false,
            ["ID"]
        );
        while ($settlementProjects->Fetch()) {
            $settlementProjectCount++;
        }
    }
    $projectSequence = $settlementProjectCount + 1;
    $projectCode = implode('-', [$regionId, $districtId, $settlementId]) . '/' . $projectSequence;

    $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => PROJECTS_IBLOCK_ID, "CODE" => "PR_WORK_TYPE"));
    while ($enum_fields = $property_enums->GetNext()) {
        $arrEnumFields[] = $enum_fields["ID"];
    }
    if (in_array($workType, $arrEnumFields)) {
        $PROP = array("PR_LOCATION" => $region, "PR_LOCATION_DISTRICT"=>$district, "PR_LOCATION_SETTLEMENT"=> $settlement, "PR_ADDRESS" => $street, "PR_WORK_TYPE" => $workType, "PR_STATUS" => 7,
            "PR_ADDITIONAL_INFO" => $dopInfo, "PR_USER_NAME" => $name, "PR_USER_PHONE" => $phone, "PR_EMAIL" => $email, "PR_USER_DATA" => $USER->GetID());
    } else {
        $PROP = array("PR_LOCATION" => $region, "PR_LOCATION_DISTRICT"=>$district, "PR_LOCATION_SETTLEMENT"=> $settlement, "PR_ADDRESS" => $street, "PR_WORK_TYPE_TEXT" => $workType, "PR_STATUS" => 7,
            "PR_ADDITIONAL_INFO" => $dopInfo, "PR_USER_NAME" => $name, "PR_USER_PHONE" => $phone, "PR_EMAIL" => $email, "PR_USER_DATA" => $USER->GetID());
    }

    $el = new CIBlockElement;
    if ($photos) {
        $files = [];
        \CFile::ConvertFilesToPost($photos, $files);
        $PROP["PR_PHOTO"] = $files;
//    $photos = resizeImage($photosOriginal,500,360);
    }
    $arLoadProductArray = array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => 1,
        "IBLOCK_ID" => PROJECTS_IBLOCK_ID,
        "PROPERTY_VALUES" => $PROP,
        "NAME" => "Проект №" . $projectCode,
        "CODE" => $projectCode,
        "ACTIVE" => "N",
    );
    if ($PRODUCT_ID = $el->Add($arLoadProductArray))
        echo $PRODUCT_ID;
    else
        echo 'NO';
}

