<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json; charset=utf-8');

// getActiveHeaderAnnouncements() определена глобально в
// local/php_interface/headerAnnounce.php (подключается через init.php).
echo json_encode(getActiveHeaderAnnouncements(), JSON_UNESCAPED_UNICODE);

// D7 ORM-кэш (getGeoHlDataClass()) нужно сбросить на диск вручную —
// см. memory geo_hlblock_migration про \CMain::FinalActions() в лёгких AJAX.
\CMain::FinalActions();
