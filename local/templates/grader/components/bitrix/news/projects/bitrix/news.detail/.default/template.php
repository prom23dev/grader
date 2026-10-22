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
    <section class="current-project">
        <div class="current-projects__main--content">
            <div class="current-project__title-and-share">
                <h1><?= $arResult['NAME'] ?></h1>
                <?include_once $_SERVER['DOCUMENT_ROOT']."/include/share_widget.php"?>
            </div>
            <? if (preg_match('/\/projects\/ongoing\/.+\//',$APPLICATION->GetCurPage())): ?>
            <h2 class="current-project__donate-title">Сбор средств</h2>
            <? else: ?>
            <h2 class="current-project__donate-title"><?=$arResult['PROPERTIES']['PR_STATUS']['VALUE']?></h2>
            <? endif;?>
            <?
            $region = $arResult['LOCATION_REGION_NAME'];
            $district = $arResult['LOCATION_DISTRICT_NAME'];
            $settlement = $arResult['LOCATION_SETTLEMENT_NAME'];
            $parts = [];

            if (!empty($region)) {
                $parts[] = $region;
            }
            if (!empty($district)) {
                $parts[] = $district;
            }
            if (!empty($settlement)) {
                $parts[] = $settlement;
            }
            ?>
            <h3 class="current-project__address-annotation">
                <?= implode(', ', $parts)?>
            </h3>
            <div class="current-project__images">
                <? foreach ($arResult['PROPERTIES']['PR_PHOTO']['VALUE'] as $img): ?>
                    <img src="<?= CFile::GetPath($img) ?>" alt="<?= $arResult['NAME'] ?>"/>
                <? endforeach; ?>
            </div>
            <div class="current-project__information-block">
                <div class="current-project__date-and-username">
                    <p><?= FormatDate("d.m.Y", MakeTimeStamp($arResult['DATE_CREATE'])) ?></p>

                    <h3><?= $arResult['USER_INFO'][0]['LOGIN'] ?></h3>
                </div>
                <? if ($arResult['PROPERTIES']['PR_WORK_TYPE']['VALUE']): ?>
                <h3 class="current-project__grading-addition-material--text"><?= $arResult['PROPERTIES']['PR_WORK_TYPE']['VALUE'] ?></h3>
                <? else: ?>
                <h3 class="current-project__grading-addition-material--text"><?= $arResult['PROPERTIES']['PR_WORK_TYPE_TEXT']['VALUE'] ?></h3>
                <? endif;?>
                <p class="current-project__description"><?= $arResult['PROPERTIES']['PR_ADDITIONAL_INFO']['VALUE'] ?></p>
                <div class="current-project__budget">
                    <? if (!empty($arResult['PROPERTIES']['PR_COST']['VALUE'])): ?>
                    <div class="current-project__project-cost">
                        <h3>Стоимость проекта</h3>
                        <h3><?= $arResult['PROPERTIES']['PR_COST']['VALUE'] ?></h3>
                    </div>
                    <? endif; ?>
                <? if (preg_match('/\/projects\/ongoing\/.+\//',$APPLICATION->GetCurPage())): ?>
                    <div class="current-project__project-total-money-collected">
                        <h3>Собрано</h3>
                        <h3><?= number_format($arResult['COLLECTED_AMOUNT'], 0, '.', ' ') ?> ₽</h3>
                    </div>
                <? endif;?>
                </div>
            </div>
            <? if (preg_match('/\/projects\/ongoing\/.+\//',$APPLICATION->GetCurPage())): ?>
                <p class="current-project__instruction">Нажав на кнопку «Поддержать проект», вы поможете собрать
                    необходимую
                    сумму для реализации этого проекта.</p>
                <button class="current-project__support-button" onclick="location.href='/projects/support.php?id=<?= $arResult['ID'] ?>';">Поддержать проект</button>
            <? else: ?>
                <button class="current-project__support-button current-project__feedback-button">Оставить отзыв</button>
            <? endif; ?>
            <? if ($arResult['PAYS']): ?>
                <div class="current-project__investors">
                    <div>
                        <h1 class="current-project__investors-title">История переводов средств</h1>
                        <div class="current-project__investors-item current-project__table_caption">
                            <h2>Дата</h2>
                            <h2>Псевдоним инвестора</h2>
                            <h2>Внесённая сумма ₽</h2>
                        </div>
                    </div>
                    <div class="current-project__investors-list">
                        <? foreach ($arResult['PAYS'] as $investor): ?>
                            <div class="current-project__investors-item">
                                <h2><?= $investor['DATE_PAID'] ?></h2>
                                <h2><?= $investor['LOGIN'] ?></h2>
                                <h2><?= $investor['AMOUNT'] ?></h2>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            <? endif; ?>
            <? if ($arResult['CAN_OBJECT']): ?>
                <div class="current-project__objection">
                    <p>Возражения по итоговому отчёту можно подать до <?= $arResult['OBJECTION_DEADLINE'] ?></p>
                    <button type="button" class="current-project__support-button" onclick="document.getElementById('objection-dialog').showModal();">Направить возражения</button>
                </div>
                <dialog class="dialog" id="objection-dialog">
                    <form id="objection-form" method="dialog">
                        <input type="hidden" name="projectId" value="<?= $arResult['ID'] ?>">
                        <div>
                            <label for="objection-text">Текст возражения</label><br>
                            <textarea required id="objection-text" name="text" rows="6"></textarea>
                        </div>
                        <div>
                            <label for="objection-files">Приложенные файлы (необязательно)</label><br>
                            <input type="file" id="objection-files" name="files[]" multiple>
                        </div>
                        <button type="submit">Отправить</button>
                    </form>
                </dialog>
                <script>
                    document.getElementById('objection-form').addEventListener('submit', function (e) {
                        e.preventDefault();
                        var formData = new FormData(this);
                        fetch('/local/templates/grader/assets/ajax/sendObjection.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(function (r) { return r.json(); })
                            .then(function (data) {
                                if (data && data.success) {
                                    window.alert('Возражение отправлено');
                                    document.getElementById('objection-dialog').close();
                                } else {
                                    window.alert((data && data.error) || 'Не удалось отправить возражение');
                                }
                            })
                            .catch(function () {
                                window.alert('Не удалось отправить возражение');
                            });
                    });
                </script>
            <? endif; ?>
        </div>
    </section>
</div>
