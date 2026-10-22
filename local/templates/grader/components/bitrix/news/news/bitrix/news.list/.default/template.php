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
?>
<div class="container news-container">
    <section class="news">
        <div class="news__main--content">
            <h1><?= $APPLICATION->GetTitle() ?></h1>
            <div class="news__list">
                <? foreach ($arResult['ITEMS'] as $news): ?>
                    <a href="<?= $news['DETAIL_PAGE_URL'] ?>">
                        <div class="news__list-item">
                            <img src="<?=resizeImage($news['DETAIL_PICTURE'], 938,428, BX_RESIZE_IMAGE_PROPORTIONAL)?>" alt="<?= $news['NAME'] ?>"/>
                            <div>
                                <p class="news__date"><?=FormatDate('d.m.Y', MakeTimeStamp($news['DATE_CREATE']));  ?></p>
                                <h2 class="news__title"><?= $news['NAME'] ?></h2>
                            </div>
                        </div>
                    </a>
                <? endforeach; ?>

            </div>
        </div>
    </section>
</div>
