<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

/**
 * Поддержка проектов оформляется через стандартный модуль sale: Bitrix\Sale\Order
 * + Payment, привязанные к проекту служебным свойством заказа PROJECT_ID
 * (тип плательщика и свойства заведены миграцией VersionSeedSaleForDonations20261008120000).
 */

function getDonationPersonTypeId(): int
{
    return (int)Option::get('main', 'grader_donation_person_type_id');
}

function getDonationOrderPropId(string $code): int
{
    static $cache = [];
    if (isset($cache[$code])) {
        return $cache[$code];
    }

    Loader::includeModule('sale');
    $personTypeId = getDonationPersonTypeId();
    $prop = \CSaleOrderProps::GetList([], ['PERSON_TYPE_ID' => $personTypeId, 'CODE' => $code])->Fetch();
    $cache[$code] = $prop ? (int)$prop['ID'] : 0;
    return $cache[$code];
}

function getTinkoffPaySystemActionId(): int
{
    static $id = null;
    if ($id !== null) {
        return $id;
    }

    $connection = Application::getConnection();
    $row = $connection->query("SELECT ID FROM b_sale_pay_system_action WHERE ACTION_FILE = 'tinkoff'")->fetch();
    $id = $row ? (int)$row['ID'] : 0;
    return $id;
}

/**
 * Список успешных переводов по проекту: дата оплаты, сумма, донор.
 * @return array<int, array{USER_ID:int, LOGIN:string, AMOUNT:float, DATE_PAID:string}>
 */
function getProjectDonations(int $projectId): array
{
    Loader::includeModule('sale');

    $propId = getDonationOrderPropId('PROJECT_ID');
    if (!$propId || !$projectId) {
        return [];
    }

    $emailPropId = getDonationOrderPropId('EMAIL');

    $connection = Application::getConnection();
    $sql = "
        SELECT o.ID AS ORDER_ID, o.USER_ID, p.DATE_PAID, p.SUM, e.VALUE AS EMAIL
        FROM b_sale_order o
        INNER JOIN b_sale_order_props_value v ON v.ORDER_ID = o.ID AND v.ORDER_PROPS_ID = " . (int)$propId . "
        INNER JOIN b_sale_order_payment p ON p.ORDER_ID = o.ID AND p.PAID = 'Y'
        LEFT JOIN b_sale_order_props_value e ON e.ORDER_ID = o.ID AND e.ORDER_PROPS_ID = " . (int)$emailPropId . "
        WHERE o.CANCELED = 'N' AND v.VALUE = '" . $connection->getSqlHelper()->forSql((string)$projectId) . "'
        ORDER BY p.DATE_PAID ASC
    ";
    $rows = $connection->query($sql)->fetchAll();

    $result = [];
    foreach ($rows as $row) {
        $userId = (int)$row['USER_ID'];
        $login = '';
        if ($userId > 0) {
            $user = \CUser::GetByID($userId)->Fetch();
            $login = $user ? $user['LOGIN'] : '';
        }
        $datePaid = $row['DATE_PAID'];
        if ($datePaid instanceof \Bitrix\Main\Type\DateTime) {
            $datePaid = $datePaid->format('d.m.Y H:i');
        }

        $result[] = [
            'USER_ID' => $userId,
            'LOGIN' => $login,
            'AMOUNT' => (float)$row['SUM'],
            'DATE_PAID' => $datePaid,
            'EMAIL' => $row['EMAIL'],
        ];
    }

    return $result;
}

/**
 * Сумма реально оплаченных пожертвований по одному проекту ("Собрано: X ₽").
 */
function getProjectCollectedAmount(int $projectId): float
{
    $sum = 0.0;
    foreach (getProjectDonations($projectId) as $donation) {
        $sum += $donation['AMOUNT'];
    }
    return $sum;
}

/**
 * То же самое, но пакетно для списка проектов (карточки в news.list) —
 * один SQL-запрос вместо N+1.
 * @param int[] $projectIds
 * @return array<int, float> ID проекта => собранная сумма
 */
function getProjectsCollectedAmounts(array $projectIds): array
{
    $projectIds = array_filter(array_unique(array_map('intval', $projectIds)));
    if (!$projectIds) {
        return [];
    }

    Loader::includeModule('sale');

    $propId = getDonationOrderPropId('PROJECT_ID');
    if (!$propId) {
        return [];
    }

    $connection = Application::getConnection();
    $idsList = implode(',', $projectIds);
    $sql = "
        SELECT v.VALUE AS PROJECT_ID, SUM(p.SUM) AS TOTAL
        FROM b_sale_order o
        INNER JOIN b_sale_order_props_value v ON v.ORDER_ID = o.ID AND v.ORDER_PROPS_ID = " . (int)$propId . "
        INNER JOIN b_sale_order_payment p ON p.ORDER_ID = o.ID AND p.PAID = 'Y'
        WHERE o.CANCELED = 'N' AND v.VALUE IN ($idsList)
        GROUP BY v.VALUE
    ";
    $rows = $connection->query($sql)->fetchAll();

    $result = [];
    foreach ($rows as $row) {
        $result[(int)$row['PROJECT_ID']] = (float)$row['TOTAL'];
    }
    return $result;
}

/**
 * Пользователи, поддержавшие проект (для права подать возражение — нужен реальный логин)
 * @return int[]
 */
function getProjectDonorUserIds(int $projectId): array
{
    $userIds = [];
    foreach (getProjectDonations($projectId) as $donation) {
        if ($donation['USER_ID'] > 0) {
            $userIds[$donation['USER_ID']] = true;
        }
    }
    return array_keys($userIds);
}

/**
 * Проекты, которые поддержал пользователь (для личного кабинета)
 * @return int[]
 */
function getUserSupportedProjectIds(int $userId): array
{
    if ($userId <= 0) {
        return [];
    }

    Loader::includeModule('sale');

    $propId = getDonationOrderPropId('PROJECT_ID');
    if (!$propId) {
        return [];
    }

    $connection = Application::getConnection();
    $sql = "
        SELECT DISTINCT v.VALUE AS PROJECT_ID
        FROM b_sale_order o
        INNER JOIN b_sale_order_props_value v ON v.ORDER_ID = o.ID AND v.ORDER_PROPS_ID = " . (int)$propId . "
        INNER JOIN b_sale_order_payment p ON p.ORDER_ID = o.ID AND p.PAID = 'Y'
        WHERE o.CANCELED = 'N' AND o.USER_ID = " . (int)$userId . "
    ";
    $rows = $connection->query($sql)->fetchAll();

    return array_map(static fn($row) => (int)$row['PROJECT_ID'], $rows);
}

/**
 * Email-адреса, указанные донорами проекта при оплате (для уведомлений)
 * @return string[]
 */
function getProjectDonorEmails(int $projectId): array
{
    $emails = [];
    foreach (getProjectDonations($projectId) as $donation) {
        if (!empty($donation['EMAIL'])) {
            $emails[mb_strtolower($donation['EMAIL'])] = $donation['EMAIL'];
        }
    }
    return array_values($emails);
}
