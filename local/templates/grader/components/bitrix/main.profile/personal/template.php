<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/**
 * Bitrix vars
 * @param array $arParams
 * @param array $arResult
 * @global CUser $USER
 * @global CMain $APPLICATION
 */
?>
    <div class="profile">
        <h1>Мой профиль</h1>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/personal/profile-tabs.php'; ?>
        <div class="my-profile__edit-block">
            <ul class="account__menu account__menu--actions profile-actions">
            <li class="account__menu-item account__exit">
                <a href="#" onclick="openDialog(leaveDialog)">
                    <span>Выйти</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17L21 12L16 7" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12H9" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
            </ul>
            <button type="button" class="edit-button">Редактировать профиль</button>
        </div>
        <form class="content" id="form">
        <? foreach ($arResult['PERSONAL_FIELDS'] as $key => $PERSONAL_FIELD): ?>
            <div class="my-profile__list">
                <? if ($key === 'LAST_NAME'): ?>
                    <div class="my-profile__item">
                        <label for="lastName">Фамилия</label>
                        <input disabled id="lastName" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
                <? if ($key === 'NAME'): ?>
                    <div class="my-profile__item">
                        <label for="firstName">Имя</label>
                        <input disabled id="firstName" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
                <? if ($key === 'SECOND_NAME'): ?>
                    <div class="my-profile__item">
                        <label for="middleName">Отчество</label>
                        <input disabled id="middleName" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
                <? if ($key === 'LOGIN'): ?>
                    <div class="my-profile__item">
                        <label for="userName">Псевдоним</label>
                        <input disabled id="userName" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
                <? if ($key === 'PERSONAL_BIRTHDAY'): ?>
                    <div class="my-profile__item">
                        <label for="birthDate">Дата рождения</label>
                        <input disabled id="birthDate" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
                <? if ($key === 'PERSONAL_GENDER'): ?>
                    <div class="my-profile__item">
                        <h4>Пол</h4>
                        <div id="gender-choose">
                            <div class="support-project__payment-variant-block">
                                <? if ($PERSONAL_FIELD==='M'): ?>
                                <label class="my-profile__gender-variant">
                                    <input disabled checked class="my-profile__gender__radio-button" id="male"
                                           name="gender" value="male" type="radio"/>
                                    <span class="radio-male">Мужской</span>
                                </label>
                                <label class="my-profile__gender-variant">
                                    <input disabled class="my-profile__gender__radio-button" id="female" name="gender"
                                           value="female" type="radio"/>
                                    <span class="radio-female">Женский</span>
                                </label>
                                <? else: ?>
                                    <label class="my-profile__gender-variant">
                                        <input disabled class="my-profile__gender__radio-button" id="male"
                                               name="gender" value="male" type="radio"/>
                                        <span class="radio-male">Мужской</span>
                                    </label>
                                    <label class="my-profile__gender-variant">
                                        <input disabled checked class="my-profile__gender__radio-button" id="female" name="gender"
                                               value="female" type="radio"/>
                                        <span class="radio-female">Женский</span>
                                    </label>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <? if ($key === 'PERSONAL_PHONE'): ?>
                    <div class="my-profile__item">
                        <label for="phone">Телефон</label>
                        <input disabled id="phone" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
                <? if ($key === 'EMAIL'): ?>
                    <div class="my-profile__item">
                        <label for="email">Почта</label>
                        <input disabled id="email" value="<?= $PERSONAL_FIELD ?>"/>
                    </div>
                <? endif; ?>
            </div>
            <? endforeach; ?>
            <div class="my-profile__save-or-discard--block profile-action-button--hidden" id="save-or-discard-block">
                <button class="discard-changes-button" id="discard-changes">Отменить</button>
                <button disabled class="save-changes-button" id="save-changes">Сохранить
                    изменения
                </button>
            </div>

        </form>
    </div>
    <dialog id="changes-dialog" class="dialog">
        <form method="dialog">
            <p>Вы внесли изменения. Сохранить их?</p>
            <div class="dialog__choose-content">
                <button class="save-changes-button" id="save-button-modal">Сохранить и выйти</button>
                <button class="discard-changes-button" id="cancel-button-modal">Отмена</button>
            </div>
        </form>
    </dialog>

<form action="<?=$arResult["AUTH_URL"]?>" hidden>
    <?foreach ($arResult["GET"] as $key => $value):?>
        <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
    <?endforeach?>
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="logout" value="yes" data-user-id="<?=$USER->getId()?>" />
    <input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
</form>
<dialog class="dialog" id="leaveDialog">
    <form method="dialog">
        <div class="leave-dialog__content">
            <h1>Выход из личного кабинета</h1>
            <p>Вы действительно хотите выйти из личного кабинета?</p>
            <div class="leave-dialog__choose-block">
                <button class="dialog-agree-button" id="leaveYes" type="button">Да</button>
                <button class="dialog-disagree-button" id="leaveNo" type="button">Нет</button>
            </div>
        </div>
    </form>
</dialog>
