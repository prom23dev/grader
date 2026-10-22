<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

// Верстка переиспользует разметку/классы детальной страницы /news/
// (current-news, current-news__information, current-news__date,
// current-news__description), только без картинки.
?>
<div class="container">
    <a href="<?= htmlspecialcharsbx($arParams["IBLOCK_URL"] ?: '/announcements/') ?>" class="announcements__back-link">&larr; Все объявления</a>
    <section class="current-news">
        <div class="current-news__main--content">
            <h1><?= $arResult['NAME'] ?></h1>
            <div class="current-news__information">
                <? if ($arResult['ACTIVE_FROM']): ?>
                    <p class="current-news__date"><?= FormatDate('d.m.Y', MakeTimeStamp($arResult['ACTIVE_FROM'])) ?></p>
                <? endif; ?>
                <? if ($arResult['DETAIL_TEXT']): ?>
                    <div class="current-news__description"><?= $arResult['DETAIL_TEXT'] ?></div>
                <? endif; ?>
            </div>
        </div>
    </section>
</div>
