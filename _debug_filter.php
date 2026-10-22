<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
$_SERVER['DOCUMENT_ROOT'] = __DIR__;
require(__DIR__ . '/bitrix/modules/main/include/prolog_before.php');

\CBitrixComponent::clearComponentCache('bitrix:news.list');
\CBitrixComponent::clearComponentCache('bitrix:news');
echo "cleared\n";

require(__DIR__ . '/bitrix/modules/main/include/epilog_after.php');
