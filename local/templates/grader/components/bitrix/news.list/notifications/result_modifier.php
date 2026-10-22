<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){ die();}
/** @var array $arResult */
CModule::IncludeModule('iblock');

foreach ($arResult['ITEMS'] as $myNotification){
    CIBlockElement::SetPropertyValueCode($myNotification['ID'], 'VIEWED', 'Y');
}
