<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
header('Content-Type: text/plain; charset=utf-8');
echo "COOKIE SELECTED_CITY_ID: " . ($_COOKIE['SELECTED_CITY_ID'] ?? '(none)') . "\n";
echo "GEO: " . print_r(getCurrentCityGeoIds(), true) . "\n";
$f = buildAnnounceGeoDbFilter();
echo "FILTER: " . print_r($f, true) . "\n";
echo "HAS ACTIVE: " . (hasActiveAnnouncements($f) ? 'YES' : 'NO') . "\n";
exit;
