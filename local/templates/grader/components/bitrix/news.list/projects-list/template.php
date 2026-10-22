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
<? if (!empty($arResult['ITEMS'])): ?>
    <?
    usort($arResult['ITEMS'], function ($a, $b) {
        $aGeneral = mb_strpos($a['NAME'], 'Общий проект') === 0 ? 0 : 1;
        $bGeneral = mb_strpos($b['NAME'], 'Общий проект') === 0 ? 0 : 1;
        return $aGeneral <=> $bGeneral;
    });
    $class = $title = $link = '';
    switch ($arParams['STATUS']) {
        case 'completed':
            $class = GetMessage("COMPLETED_CLASS");
            $title = GetMessage("COMPLETED_TITLE");
            $link = '/projects/result/';
            break;
        case 'ongoing':
            $class = GetMessage("ONGOING_CLASS");
            $title = GetMessage("ONGOING_TITLE");
            $link = '/projects/';
            break;
    }
    ?>
    <section class="<?= $class ?>-projects main-page__<?= $class ?>">
        <div class="<?= $class ?>-projects__main--content">
            <div class="projects__header-wrapper">
                <h1><?= $title ?></h1>
                <div class="projects__header-actions">
                    <a class="projects__look-all" href="<?= $link ?>">Смотреть все</a>
                    <? if ($USER->IsAuthorized()): ?>
                        <button onclick="redirect('/projects/create/')" class="main-page__create-button projects__create-button">Создать свой проект
                        </button>
                    <? else: ?>
                        <button onclick="openAuth('/projects/create/');" class="main-page__create-button projects__create-button">Авторизоваться и создать свой проект
                        </button>
                    <? endif; ?>
                </div>
            </div>
            <div class="projects__content">
                <div class="<?= $class ?>-projects__list">
                    <? foreach ($arResult['ITEMS'] as $project): ?>
                        <div class="<?= $class ?>-projects__list-item">
                            <div class="<?= $class ?>-projects__pictures">
                                <? if ($arParams['STATUS'] === 'ongoing'): ?>
                                    <img class="<?= $class ?>-projects__image"
                                         src="<?= resizeImage($project['PROPERTIES']['PR_PHOTO']['VALUE'][0], 500, 360, BX_RESIZE_IMAGE_PROPORTIONAL) ?>"
                                         alt="<?= $project['NAME'] ?>"/>
                                <? endif; ?>
                                <? if ($arParams['STATUS'] === 'completed'): ?>
                                    <? foreach ($project['PROPERTIES']['PR_PHOTO']['VALUE'] as $img): ?>
                                        <img class="<?= $class ?>-projects__image"
                                             src="<?= resizeImage($img, 250, 360, BX_RESIZE_IMAGE_EXACT) ?>"
                                             alt="<?= $project['NAME'] ?>"/>
                                    <? endforeach; ?>
                                <? endif; ?>
                            </div>

                            <div class="<?= $class ?>-projects__information">
                                <h2 class="<?= $class ?>-projects__address"><?= $project['NAME'] ?></h2>

                                <div class="<?= $class ?>-projects__information-item">
                                    <h3><?= $project['PROPERTIES']['PR_REGION']['VALUE'] ?></h3>
                                </div>

                                <div class="<?= $class ?>-projects__information-item">
                                    <h3>Собрано</h3>
                                    <h3><?= number_format($project['COLLECTED_AMOUNT'], 0, '.', ' ') ?> ₽</h3>
                                </div>

                                <?php /**?>
                                 * <div class="<?=$class?>-projects__information-item">
                                 * <h3>Площадь</h3>
                                 * <h3><?=$project['PROPERTIES']['PR_SQUARE']['VALUE']?> м²</h3>
                                 * </div>
                                 * <div class="<?=$class?>-projects__information-item">
                                 * <h3>Сроки</h3>
                                 * <h3><?=periodDayOrMonth($project['PROPERTIES']['PR_DEADLINES']['VALUE'])?></h3>
                                 * </div>
                                 * <div class="<?=$class?>-projects__information-item">
                                 * <?$budget = str_replace(array(' ','₽'),'', $project['PROPERTIES']['PR_COST']['VALUE']);?>
                                 * <h3>Бюджет</h3>
                                 * <h3><?=number_format(intval($budget), 0,'.', ' ') ?> ₽</h3>
                                 * </div>
                                 * <? */ ?>
                            </div>
                            <a href="<?= $project['DETAIL_PAGE_URL'] ?>">
                                <button class="<?= $class ?>-projects__details-button" type="button">Подробнее</button>
                            </a>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <? if ($arParams['DISPLAY_BOTTOM_PAGER'] !== 'N' && !empty($arResult['NAV_STRING'])): ?>
        <div id="pag"><?= $arResult['NAV_STRING'] ?></div>
    <? endif; ?>
<? endif; ?>
