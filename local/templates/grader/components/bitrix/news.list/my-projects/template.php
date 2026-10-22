<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
<div class="profile">
    <h1>Мои проекты</h1>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/personal/profile-tabs.php'; ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:search.form",
        "grader-search",
        array(
            "PAGE" => "",
            "USE_SUGGEST" => "N"
        )
    ); ?>
<? if (!empty($arResult['ITEMS'])):?>
    <div class="create-project__create-button-block">
        <button onclick="redirectToCreateProject()" class="create-project__create-button">Создать проект</button>
    </div>
    <div class="my-projects__projects-history">
        <? foreach ($arResult['ITEMS'] as $myProject): ?>
        <div class="my-projects__projects-item">
            <div class="my-projects__projects-item__date-and-status">
                <p><?=date("d.m.Y", strtotime($myProject['DATE_CREATE'])) ?></p>
                <p class="my-projects__projects-item--status"><?=$myProject['PROPERTIES']['PR_STATUS']['VALUE']?></p>
            </div>
            <div class="my-projects__projects-item__information">
                <a href="<?=$myProject['DETAIL_PAGE_URL']?>"><h5><?=$myProject['NAME']?></h5></a>
                <p><?=$myProject['PROPERTIES']['PR_ADDRESS']['VALUE']?></p>
                <p><?=$myProject['PROPERTIES']['PR_WORK_TYPE']['VALUE']?></p>
            </div>
        </div>
        <? endforeach; ?>
        <div class="my-projects__not-yet-information-block">
        </div>
    </div>
<? else: ?>
    <div class="my-projects__not-yet-information-block">
        <h3>Пока здесь нет ни одного проекта.</h3>
        <div class="create-project__create-button-block">
            <button onclick="redirectToCreateProject()" class="create-project__create-button">Создать проект</button>
        </div>
    </div>
<? endif; ?>
</div>
