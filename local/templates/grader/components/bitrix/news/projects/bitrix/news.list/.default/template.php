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
<style>
    .projects-page { padding: 0 !important; margin-top: 0 !important; }
</style>
<? $isAllProjects = $APPLICATION->GetCurPage(false) === '/projects/'; ?>
<? $projectsPath = $APPLICATION->GetCurPage(false); ?>
<?
$hasProjectValue = static function ($value): bool {
    $value = trim((string)$value);
    return $value !== '' && $value !== '0' && $value !== '0 ₽';
};
$formatProjectBudget = static function ($value): string {
    $budget = str_replace([' ', '₽'], '', trim((string)$value));
    return number_format((float)$budget, 0, '.', ' ') . ' ₽';
};
?>
<? if ($isAllProjects || $APPLICATION->GetCurPage(false) === '/projects/ongoing/'): ?>
    <div class="container projects-container">
        <section class="projects-page">
            <div class="projects-page__main--content">
                <h1><?= $isAllProjects ? 'Все проекты' : 'Действующие проекты' ?></h1>
                <nav class="projects-tabs" aria-label="Разделы проектов">
                    <a class="projects-tabs__item<?= $projectsPath === '/projects/ongoing/' ? ' is-active' : '' ?>" href="/projects/ongoing/">Действующие проекты</a>
                    <a class="projects-tabs__item<?= $projectsPath === '/projects/result/' ? ' is-active' : '' ?>" href="/projects/result/">Завершенные проекты</a>
                </nav>
                <? $APPLICATION->IncludeComponent("bitrix:search.form", "grader-search", array("PAGE" => "", "USE_SUGGEST" => "N")); ?>
                <div class="projects-page__list">
                    <? foreach ($arResult['ITEMS'] as $project): ?>
                        <a class="projects-page__list-item" href="<?= $project['DETAIL_PAGE_URL'] ?>">
                            <img class="projects-page__image"
                                 src="<?= resizeImage($project['PROPERTIES']['PR_PHOTO']['VALUE'][0], 500, 360, BX_RESIZE_IMAGE_PROPORTIONAL) ?>"
                                 alt="<?= $project['NAME'] ?>"/>
                            <div class="projects-page__information">
                                <? if ($hasProjectValue($project['PROPERTIES']['PR_ADDRESS']['VALUE'] ?? '')): ?><div>
                                    <h2 class="projects-page__address"><?= $project['PROPERTIES']['PR_ADDRESS']['VALUE'] ?></h2>
                                </div>
                                <? endif; ?>
                                <div class="projects-page__information-item">
                                    <h3><?= $project['NAME'] ?></h3>
                                </div>
                                <? if ($hasProjectValue($project['PROPERTIES']['PR_SQUARE']['VALUE'] ?? '')): ?><div class="projects-page__information-item">
                                    <h3>Площадь</h3>
                                    <h3><?= $project['PROPERTIES']['PR_SQUARE']['VALUE'] ?> м²</h3>
                                </div><? endif; ?>
                                <? if ($hasProjectValue($project['PROPERTIES']['PR_DEADLINES']['VALUE'] ?? '')): ?><div class="projects-page__information-item">
                                    <h3>Сроки</h3>
                                    <h3><?= $project['PROPERTIES']['PR_DEADLINES']['VALUE'] ?> дней</h3>
                                </div><? endif; ?>
                                <? if ((float)str_replace([' ', '₽', ','], ['', '', '.'], (string)($project['PROPERTIES']['PR_COST']['VALUE'] ?? '')) > 0): ?><div class="projects-page__information-item">
                                    <h3>Бюджет</h3>
                                    <?$budget = str_replace(array(' ','₽'),'', $project['PROPERTIES']['PR_COST']['VALUE']);?>
                                    <h3><?=number_format(intval($budget), 0,'.', ' ') ?> ₽</h3>
                                </div><? endif; ?>
                                <? if ($hasProjectValue($project['PROPERTIES']['PR_STATUS']['VALUE'] ?? '')): ?><div class="projects-page__information-item">
                                    <h3>Статус</h3>
                                    <h3><?= $project['PROPERTIES']['PR_STATUS']['VALUE'] ?></h3>
                                </div><? endif; ?>
                            </div>
                            <div class="projects-page__details-button">
                                Подробнее
                            </div>
                        </a>
                    <? endforeach; ?>
                </div>
                <div id="pag"><?= $arResult["NAV_STRING"] ?></div>
            </div>
        </section>
    </div>
<? else: ?>
    <div class="container projects-container">
        <section class="projects-page">
            <div class="projects-page__main--content">
                <h1>Результаты</h1>
                <nav class="projects-tabs" aria-label="Разделы проектов">
                    <a class="projects-tabs__item<?= $projectsPath === '/projects/ongoing/' ? ' is-active' : '' ?>" href="/projects/ongoing/">Действующие проекты</a>
                    <a class="projects-tabs__item<?= $projectsPath === '/projects/result/' ? ' is-active' : '' ?>" href="/projects/result/">Завершенные проекты</a>
                </nav>
                <? $APPLICATION->IncludeComponent("bitrix:search.form", "grader-search", array("PAGE" => "", "USE_SUGGEST" => "N")); ?>
                <div class="projects-page__list">
                    <? foreach ($arResult['ITEMS'] as $readyProject): ?>
                        <div class="projects-page__list-item">
                            <div class="projects-page__pictures">
                                <? foreach ($readyProject['PROPERTIES']['PR_PHOTO']['VALUE'] as $img): ?>
                                    <img class="projects-page__image" src="<?= CFile::GetPath($img) ?>"
                                         alt="<?= $readyProject['NAME'] ?>"/>
                                <? endforeach; ?>
                            </div>

                            <div class="projects-page__information">
                                <div>
                                    <h2 class="projects-page__address"><?= $readyProject['PROPERTIES']['PR_ADDRESS']['VALUE'] ?></h2>
                                </div>
                                <div class="projects-page__information-item">
                                    <h3><?= $readyProject['NAME'] ?></h3>
                                </div>
                                <div class="projects-page__information-item">
                                    <h3>Площадь</h3>
                                    <h3><?= $readyProject['PROPERTIES']['PR_SQUARE']['VALUE'] ?> м²</h3>
                                </div>
                                <div class="projects-page__information-item">
                                    <h3>Сроки</h3>
                                    <h3><?= $readyProject['PROPERTIES']['PR_DEADLINES']['VALUE'] ?> дней</h3>
                                </div>
                                <div class="projects-page__information-item">
                                    <h3>Бюджет</h3>
                                    <?$budget = str_replace(array(' ','₽'),'', $readyProject['PROPERTIES']['PR_COST']['VALUE']);?>
                                    <h3><?=number_format(intval($budget), 0,'.', ' ') ?> ₽</h3>
                                </div>
                            </div>
                            <a href="<?= $readyProject['DETAIL_PAGE_URL']?>">
                                <button class="projects-page__details-button" type="button">
                                    Подробнее
                                </button>
                            </a>

                        </div>
                    <? endforeach; ?>
                </div>
                <div id="pag"><?= $arResult["NAV_STRING"] ?></div>
        </section>
    </div>
<? endif; ?>
