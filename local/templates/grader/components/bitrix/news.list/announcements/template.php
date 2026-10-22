<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var CBitrixComponentTemplate $this */
$this->setFrameMode(true);
?>
<? if (!empty($arResult["ITEMS"])): ?>
    <div class="announcements__list">
        <? foreach ($arResult["ITEMS"] as $announcement): ?>
            <?
            $link = $announcement["PROPERTIES"]["ANNOUNCE_LINK"]["VALUE"] ?? '';
            $date = $announcement["ACTIVE_FROM"] ? FormatDate('d.m.Y', MakeTimeStamp($announcement["ACTIVE_FROM"])) : '';
            ?>
            <div class="announcements__item">
                <? if ($date): ?>
                    <p class="announcements__item-date"><?= $date ?></p>
                <? endif; ?>
                <p class="announcements__item-text"><?= $announcement["NAME"] ?></p>
                <? if ($link): ?>
                    <a href="<?= htmlspecialcharsbx($link) ?>" class="announcements__item-link">Подробнее</a>
                <? endif; ?>
            </div>
        <? endforeach; ?>
    </div>
    <div class="announcements__pagination"><?= $arResult["NAV_STRING"] ?></div>
<? else: ?>
    <p class="announcements__empty">Для вашего города объявлений пока нет.</p>
<? endif; ?>
