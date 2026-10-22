<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$e = static fn($value) => htmlspecialcharsbx((string)$value);
$props = $arResult['PROPERTIES'];
$photos = $props['PR_PHOTO']['VALUE'] ?? [];

$photos = is_array($photos) ? $photos : ($photos ? [$photos] : []);
$collected = (float)($arResult['COLLECTED_AMOUNT'] ?? $props['PR_ITOG']['VALUE'] ?? 0);
$goal = (float)str_replace([' ', '₽', ','], ['', '', '.'], (string)($props['PR_FUND_GOAL']['VALUE'] ?? $props['PR_COST']['VALUE'] ?? 0));
$percent = $goal > 0 ? min(100, round($collected / $goal * 100)) : 0;
$settlement = $arResult['LOCATION_SETTLEMENT_NAME'] ?: ($props['PR_LOCATION_SETTLEMENT']['VALUE'] ?? '');
$region = $arResult['LOCATION_REGION_NAME'] ?: ($props['PR_LOCATION']['VALUE'] ?? '');
$crestPath = '';
if (function_exists('getGeoSettlementCoatPath')) {
    $crestPath = getGeoSettlementCoatPath((string)($props['PR_LOCATION_SETTLEMENT']['VALUE'] ?? $settlement));
}
if (function_exists('getGeoSettlementName')) {
    $settlement = getGeoSettlementName($settlement);
}
if (function_exists('getGeoRegionName')) {
    $region = getGeoRegionName($region);
}
$crest = $props['PR_CITY_COAT']['VALUE'] ?? 0;
if (!$crestPath && $crest) {
    $crestPath = CFile::GetPath($crest);
}
$video = trim((string)($props['PR_HERO_VIDEO']['VALUE'] ?? ''));
$photos = array_values(array_filter(array_map(static fn($id) => CFile::GetPath($id), $photos)));
?>
<main class="pd-page">
    <div class="pd-wrap">
        <div class="pd-place" aria-label="Населённый пункт">
            <?php if ($crestPath): ?><img src="<?= $e($crestPath) ?>" alt="Герб <?= $e($settlement) ?>"><?php else: ?><span class="pd-place__fallback">⚜</span><?php endif; ?>
            <strong><?= $e($settlement ?: 'Населённый пункт') ?></strong>
        </div>
           <section class="pd-hero">
            <div class="pd-hero-copy">
                <div class="pd-status"><i></i><?= preg_match('#/projects/ongoing/#', $APPLICATION->GetCurPage()) ? 'Сбор средств открыт' : $e($props['PR_STATUS']['VALUE']) ?></div>
                <h1><?= $e($arResult['NAME']) ?></h1>
                <p class="pd-lead"><?= $e($props['PR_ADDITIONAL_INFO']['VALUE'] ?: $arResult['PREVIEW_TEXT']) ?></p>
            </div>
            <aside class="pd-fund">
                <div class="pd-fund__top"><div><span>Собрано на сегодня</span><b><?= number_format($collected, 0, '.', ' ') ?> ₽</b></div><em><?= $percent ?>%</em></div>
                <div class="pd-progress"><i style="width:<?= $percent ?>%"></i></div>
                <div class="pd-fund__meta"><span>Цель <?= $goal ? number_format($goal, 0, '.', ' ') . ' ₽' : 'уточняется' ?></span><span><?= $e($props['PR_FUND_DEADLINE']['VALUE'] ? 'До '.$props['PR_FUND_DEADLINE']['VALUE'] : 'Сбор открыт') ?></span></div>
                <?php if (preg_match('#/projects/ongoing/#', $APPLICATION->GetCurPage())): ?><a class="pd-button" href="/projects/support.php?id=<?= (int)$arResult['ID'] ?>">Поддержать проект <span>↗</span></a><?php endif; ?>
                <small>Безопасная оплата · Договор оферты</small>
            </aside>
        </section>

        <section class="pd-section"><div class="pd-section__head"><div><h2>О проекте</h2><p>Зачем это нужно городу и что изменится</p></div></div><div class="pd-about"><article class="pd-card"><p><?= $e($props['PR_ADDITIONAL_INFO']['VALUE'] ?: $arResult['DETAIL_TEXT']) ?></p><div class="pd-tags"><span><?= $e($props['PR_WORK_TYPE']['VALUE'] ?: 'Городская среда') ?></span><span><?= $e($region) ?></span></div></article><div class="pd-facts"><div><b><?= $e($props['PR_SQUARE']['VALUE'] ?: '—') ?></b><span>площадь проекта</span></div><div><b><?= $e($props['PR_DEADLINES']['VALUE'] ?: '—') ?></b><span>срок реализации</span></div><div><b><?= $e($props['PR_COST']['VALUE'] ?: '—') ?></b><span>стоимость проекта</span></div><div><b><?= count($arResult['PAYS'] ?? []) ?></b><span>инвесторов</span></div></div></div></section>

        <section class="pd-section"><div class="pd-section__head"><div><h2>Статус проекта</h2><p>Прозрачно показываем, где мы сейчас</p></div></div><div class="pd-stages"><div class="done"><i>✓</i><span>Идея</span></div><div class="done"><i>✓</i><span>Проектирование</span></div><div class="current"><i>3</i><span><?= $e($props['PR_STATUS']['VALUE'] ?: 'Сбор средств') ?></span></div><div><i>4</i><span>Реализация</span></div></div></section>

        <?php if ($photos || $video): ?><section class="pd-section"><div class="pd-section__head"><div><h2>Как это выглядит</h2><p>Эскизы, место и материалы проекта</p></div></div><div class="pd-gallery"><?php if ($video): ?><a class="pd-media pd-media--video" href="<?= $e($video) ?>" target="_blank" rel="noopener"><span class="pd-play">▶</span><span>Смотреть видео проекта</span></a><?php endif; ?><?php foreach ($photos as $index => $photo): ?><div class="pd-media" style="background-image:url('<?= $e($photo) ?>')"><span><?= $index === 0 ? 'Главное фото проекта' : 'Материалы проекта' ?></span></div><?php endforeach; ?></div></section><?php endif; ?>

        <?php if (!empty($arResult['PAYS'])): ?><section class="pd-section"><div class="pd-section__head"><div><h2>Команда и инвесторы</h2><p>Люди, которые уже помогают проекту случиться</p></div><span><?= count($arResult['PAYS']) ?> участников</span></div><div class="pd-investors"><?php foreach ($arResult['PAYS'] as $investor): ?><div><span class="pd-person">◎</span><div><b><?= $e($investor['LOGIN'] ?: 'Анонимный инвестор') ?></b><span>Инвестор · <?= $e($investor['AMOUNT']) ?> ₽</span></div></div><?php endforeach; ?></div></section><?php endif; ?>
    </div>
</main>
