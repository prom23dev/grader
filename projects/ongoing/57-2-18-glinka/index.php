<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->SetTitle('Общий проект. Глинка');

$APPLICATION->IncludeComponent('bitrix:news.detail', 'project-detail', [
    'IBLOCK_TYPE' => 'company',
    'IBLOCK_ID' => 1,
    'ELEMENT_ID' => 447743,
    'ELEMENT_CODE' => '57-2-18-glinka',
    'FIELD_CODE' => ['ID', 'CODE', 'NAME', 'DATE_CREATE', 'PREVIEW_TEXT', 'DETAIL_TEXT'],
    'PROPERTY_CODE' => [
        'PR_LOCATION', 'PR_LOCATION_SETTLEMENT', 'PR_WORK_TYPE', 'PR_ADDITIONAL_INFO',
        'PR_SQUARE', 'PR_DEADLINES', 'PR_COST', 'PR_ITOG', 'PR_STATUS', 'PR_PHOTO',
        'PR_CITY_COAT', 'PR_FUND_GOAL', 'PR_FUND_DEADLINE', 'PR_HERO_VIDEO', 'PR_MILESTONES',
    ],
    'CHECK_DATES' => 'Y',
    'SET_TITLE' => 'Y',
    'CACHE_TYPE' => 'A',
    'CACHE_TIME' => '3600',
]);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
