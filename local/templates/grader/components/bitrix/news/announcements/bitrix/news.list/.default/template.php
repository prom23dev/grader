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

// Верстка переиспользует разметку/классы страницы /news/ (news-container,
// news, news__list, news__list-item, news__date, news__title), только без
// картинки — у объявлений нет свойства изображения. Список уже
// отфильтрован по гео (см. announcements/index.php): если для города
// посетителя ничего нет, фильтр снят и здесь показываются все объявления.
?>
<div class="container news-container">
    <section class="news">
        <div class="news__main--content">
            <h1>Объявления</h1>
            <? if (!empty($arResult["ITEMS"])): ?>
                <div class="news__list">
                    <? foreach ($arResult["ITEMS"] as $announcement): ?>
                        <?
                        $date = $announcement["ACTIVE_FROM"] ? FormatDate('d.m.Y', MakeTimeStamp($announcement["ACTIVE_FROM"])) : '';
                        ?>
                        <a href="<?= $announcement["DETAIL_PAGE_URL"] ?>">
                            <div class="news__list-item news__list-item--no-image">
                                <div>
                                    <? if ($date): ?>
                                        <p class="news__date"><?= $date ?></p>
                                    <? endif; ?>
                                    <h2 class="news__title"><?= $announcement["NAME"] ?></h2>
                                </div>
                            </div>
                        </a>
                    <? endforeach; ?>
                </div>
                <div class="announcements__pagination"><?= $arResult["NAV_STRING"] ?></div>
            <? else: ?>
                <p class="announcements__empty">Для вашего города объявлений пока нет.</p>
            <? endif; ?>
        </div>
    </section>
</div>
