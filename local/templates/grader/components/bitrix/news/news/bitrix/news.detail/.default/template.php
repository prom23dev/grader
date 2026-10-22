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

<div class="container">
    <section class="current-news">
        <div class="current-news__main--content">
            <h1><?= $arResult['NAME'] ?></h1>
            <div class="current-news__information">
                <p class="current-news__date"><?= FormatDate('d.m.Y', MakeTimeStamp($arResult['DATE_CREATE'])) ?></p>
                <img class="current-news__image" src="<?= $arResult['DETAIL_PICTURE']['SRC']?>" alt="currentNews"/>
                <div class="current-news__description">
                    <p><?= $arResult['DETAIL_TEXT']?></p>
                </div>
            </div>
        </div>
    </section>
</div>
