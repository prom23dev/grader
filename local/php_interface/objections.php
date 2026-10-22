<?php

use Bitrix\Main\Config\Option;

const OBJECTIONS_WINDOW_DAYS = 5;

function getObjectionsIblockId(): int
{
    return (int)Option::get('main', 'grader_objections_iblock_id');
}

/**
 * Дата+время, до которой можно подать возражение по отчёту проекта (report date + 5 календарных дней),
 * или null если отчёт ещё не опубликован.
 */
function getProjectObjectionDeadline(string $reportDateValue): ?\DateTime
{
    if (!$reportDateValue) {
        return null;
    }

    $reportDate = \DateTime::createFromFormat('d.m.Y H:i:s', $reportDateValue)
        ?: \DateTime::createFromFormat('d.m.Y', $reportDateValue);
    if (!$reportDate) {
        return null;
    }

    $reportDate->modify('+' . OBJECTIONS_WINDOW_DAYS . ' days');
    return $reportDate;
}

/**
 * Может ли текущий пользователь подать возражение по проекту прямо сейчас
 */
function canSubmitObjection(int $userId, int $projectId, string $reportDateValue): bool
{
    if ($userId <= 0 || $projectId <= 0) {
        return false;
    }

    $deadline = getProjectObjectionDeadline($reportDateValue);
    if (!$deadline || $deadline < new \DateTime()) {
        return false;
    }

    return in_array($userId, getProjectDonorUserIds($projectId), true);
}
