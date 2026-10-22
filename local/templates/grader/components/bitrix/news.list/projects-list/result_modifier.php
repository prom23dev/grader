<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

// "Собрано" на карточках считается из реально оплаченных пожертвований
// (см. local/php_interface/projectDonations.php), одним пакетным запросом
// на весь список вместо N+1.
if (!empty($arResult['ITEMS'])) {
    $projectIds = array_column($arResult['ITEMS'], 'ID');
    $collectedAmounts = getProjectsCollectedAmounts($projectIds);

    foreach ($arResult['ITEMS'] as &$project) {
        $project['COLLECTED_AMOUNT'] = $collectedAmounts[(int)$project['ID']] ?? 0.0;
    }
    unset($project);
}
