<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
$_SERVER['DOCUMENT_ROOT'] = __DIR__;
$_COOKIE['SELECTED_CITY_ID'] = '21959';
require(__DIR__ . '/bitrix/modules/main/include/prolog_before.php');

\Bitrix\Main\Loader::includeModule('iblock');
$iblockId = getHeaderAnnounceIblockId();
$filter = buildAnnounceGeoDbFilter();
$filter = array_merge(['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y'], $filter);
$res = \CIBlockElement::GetList([], $filter, false, false, ['ID', 'NAME']);
$items = [];
while ($row = $res->Fetch()) { $items[] = $row['ID'] . ': ' . $row['NAME']; }
echo "COUNT: " . count($items) . "\n";
echo implode("\n", $items) . "\n";

require(__DIR__ . '/bitrix/modules/main/include/epilog_after.php');
