<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
$this->setFrameMode(true);
global $USER;
?>
<div class="my-projects">
    <h1>Проекты, которые я поддержал</h1>
<? if (!empty($arResult['ITEMS'])):?>
    <div class="my-projects__projects-history">
        <? foreach ($arResult['ITEMS'] as $project): ?>
        <?
            $reportDateValue = $project['PROPERTIES']['PR_REPORT_DATE']['VALUE'] ?? '';
            $deadline = getProjectObjectionDeadline($reportDateValue);
            $canObject = canSubmitObjection((int)$USER->GetID(), (int)$project['ID'], $reportDateValue);
        ?>
        <div class="my-projects__projects-item">
            <div class="my-projects__projects-item__date-and-status">
                <p><?=date("d.m.Y", strtotime($project['DATE_CREATE'])) ?></p>
                <p class="my-projects__projects-item--status"><?=$project['PROPERTIES']['PR_STATUS']['VALUE']?></p>
            </div>
            <div class="my-projects__projects-item__information">
                <a href="<?=$project['DETAIL_PAGE_URL']?>"><h5><?=$project['NAME']?></h5></a>
                <p><?=$project['PROPERTIES']['PR_ADDRESS']['VALUE']?></p>
                <? if ($canObject): ?>
                    <p>Можно направить возражения по отчёту до <?=$deadline->format('d.m.Y H:i')?> — <a href="<?=$project['DETAIL_PAGE_URL']?>">перейти на страницу проекта</a></p>
                <? endif; ?>
            </div>
        </div>
        <? endforeach; ?>
    </div>
<? else: ?>
    <div class="my-projects__not-yet-information-block">
        <h3>Вы пока не поддержали ни одного проекта.</h3>
    </div>
<? endif; ?>
</div>
