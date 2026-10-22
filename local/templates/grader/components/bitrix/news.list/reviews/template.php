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
<? if (!empty($arResult['ITEMS'])):?>
    <div class="container">
        <section class="reviews">
            <div class="reviews__main--content">
                <h1>Отзывы</h1>
            </div>
            <div class="reviews__list">
                <? foreach ($arResult["ITEMS"] as $review): ?>
                    <?
                    $this->AddEditAction($review['ID'], $review['EDIT_LINK'], CIBlock::GetArrayByID($review["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($review['ID'], $review['DELETE_LINK'], CIBlock::GetArrayByID($review["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <div class="reviews__item">
                        <div class="reviews__item--header">
                            <p class="reviews__item--date"><?= FormatDate('d.m.Y', MakeTimeStamp($review['DATE_CREATE'])) ?></p>
                            <p class="reviews__item--username"><?= $review['NAME'] ?></p>
                        </div>
                        <p class="reviews__item--content"><?= $review['DETAIL_TEXT'] ?></p>
                        <button class="reviews__mobile-show-more" data-state="collapsed" href="#">Читать полностью</button>
                    </div>
                <? endforeach; ?>
            </div>
            <div id="pag"><?= $arResult["NAV_STRING"] ?></div>
            <div class="reviews__send-review">
                <p>Оставить отзыв</p>
                <form class="reviews__send-review-form">
                    <label>
                    <textarea id="review-text" name="review" required class="reviews__send-review-input"
                              type="text"></textarea>
                    </label>
                    <?php
                    $consentIdPrefix = 'review';
                    include $_SERVER['DOCUMENT_ROOT'] . '/include/consent-checkboxes.php';
                    ?>
                    <a href="#">
                        <button class="reviews_send-review--button" type="submit">Отправить</button>
                    </a>
                </form>
            </div>
        </section>
    </div>

    <!--Успешный отзыв-->
    <dialog id="success-send-review-dialog" class="dialog">
        <div class="close-dialog">
            <a href="#">
                <button onclick="closeDialog(successDialog)">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z"
                              fill="#474A51"/>
                    </svg>
                </button>
            </a>
        </div>
        <div class="success-send-review">
            <div class="modal-content">
                <h1>Готово</h1>
                <div class="success-send-review__message">
                    <p>Отзыв успешно отправлен.</p>
                    <br/>
                    <p> Он будет опубликован на странице Отзывы.</p>
                </div>
            </div>
        </div>
    </dialog>

    <!--Неуспешный отзыв-->
    <dialog id="failed-send-review-dialog" class="dialog">
        <div class="failed-send-review">
            <div class="modal-content">
                <h1>Чтобы отправить отзыв необходимо авторизоваться</h1>
                <div class="modal__choose-block">
                    <a href="#">
                        <button type="submit" class="modal__go-to-auth" onclick="{openAuth();closeDialog(failedDialog)}">Авторизация</button>
                    </a>
                    <a href="#">
                        <button type="button" onclick="closeDialog(failedDialog)" class="modal__failed-close">Отмена
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </dialog>
<? else: ?>
    <div class="container">
        <section class="reviews">
            <div class="reviews__main--content">
                <h1>Отзывов пока нет</h1>
            </div>
        </section>
    </div>
<? endif;?>
<!--Согласие на обработку-->
<dialog id="processing-consent-dialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button  onclick="closeDialog(document.getElementById('processing-consent-dialog'))">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="processing-consent">
        <div class="modal-content">
            <h1>Внимание!</h1>
            <div class="modal__choose-block">
                <a href="#">
                    <button type="submit" class="modal__go-to-auth" onclick="{openAuth();closeDialog(failedDialog)}">Авторизация</button>
                </a>
                <a href="#">
                    <button type="button" onclick="closeDialog(failedDialog)" class="modal__failed-close">Отмена
                    </button>
                </a>
            </div>
        </div>
    </div>
</dialog>
