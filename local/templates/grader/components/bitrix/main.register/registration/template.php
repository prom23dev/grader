<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2024 Bitrix
 */

use Bitrix\Main\Web\Json;

/**
 * Bitrix vars
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<!--Регистрация-->
<dialog id="registrationDialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button onclick="closeRegistrationDialog()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M17.5608 13.0608L10.0608 20.5608L7.93945 18.4395L14.3788 12.0001L7.93945 5.56077L10.0608 3.43945L17.5608 10.9395C18.1466 11.5252 18.1466 12.475 17.5608 13.0608Z"
                          fill="#474A51"/>
                </svg>
            </button>
        </a>
        <a href="#">
            <button onclick="closeRegistrationDialog()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z"
                          fill="#474A51"/>
                </svg>

            </button>
        </a>
    </div>

    <div class="registration">

        <div class="modal-content">
            <h1><?= GetMessage("AUTH_REGISTER")?></h1>
        </div>
        <form class="content" id="regForm" method="post" action="" name="regform">
            <div class="registration__form">
                <? foreach ($arResult["SHOW_FIELDS"] as $FIELD): ?>
                    <? switch ($FIELD): case "LAST_NAME": ?>
                        <div class="modal-field">
                            <label for="lastName"><span
                                        class="required-sign">*</span><?= GetMessage("REGISTER_FIELD_LAST_NAME") ?>
                            </label>
                            <input required id="lastName" name="REGISTER[<?= $FIELD ?>]"
                                   placeholder="<?= GetMessage("REGISTER_FIELD_LAST_NAME") ?>"/>
                        </div>
                        <? break; ?>
                    <? case "NAME": ?>
                        <div class="modal-field">
                            <label for="firstName"><span
                                        class="required-sign">*</span><?= GetMessage("REGISTER_FIELD_NAME") ?></label>
                            <input required id="firstName" name="REGISTER[<?= $FIELD ?>]"
                                   placeholder="<?= GetMessage("REGISTER_FIELD_NAME") ?>"/>
                        </div>
                        <? break; ?>
                    <? case "SECOND_NAME": ?>
                        <div class="modal-field">
                            <label for="middleName"><span class="required-sign">*</span><?= GetMessage("REGISTER_FIELD_SECOND_NAME") ?></label>
                            <input required id="middleName" name="REGISTER[<?= $FIELD ?>]"
                                   placeholder="<?= GetMessage("REGISTER_FIELD_SECOND_NAME") ?>"/>
                        </div>
                        <? break; ?>
                    <? case "LOGIN": ?>
                        <div class="modal-field">
                            <label for="userName"><span class="required-sign">*</span><?= GetMessage("REGISTER_FIELD_LOGIN") ?></label>
                            <input required id="userName" name="REGISTER[<?= $FIELD ?>]"
                                   placeholder="<?= GetMessage("REGISTER_FIELD_LOGIN") ?>"/>
                        </div>
                        <? break; ?>
                    <? case "PASSWORD": ?>
                    <? $generatedPassword = gen_password()?>
                        <input type="hidden" id="main-password" name="REGISTER[<?= $FIELD ?>]" placeholder="<?= GetMessage("REGISTER_FIELD_PASSWORD")?>" value="<?=$generatedPassword?>"/>
                    <? break; ?>
                    <? case "CONFIRM_PASSWORD": ?>
                        <input type="hidden" id="conf-password" name="REGISTER[<?= $FIELD ?>]" placeholder="<?= GetMessage("REGISTER_FIELD_CONFIRM_PASSWORD")?>" value="<?=$generatedPassword?>"/>
                    <? break; ?>
                    <? case "PERSONAL_BIRTHDAY": ?>
                        <div class="modal-field">
                            <label for="birthDate"><?=GetMessage("REGISTER_FIELD_PERSONAL_BIRTHDAY")?></label>
                            <input type="date" id="birthDate" placeholder="ХХ.ХХ.ХХХХ" name="REGISTER[<?= $FIELD ?>]" value="<?=$arResult["VALUES"][$FIELD]?>" />
                        </div>
                    <? break; ?>
                    <? case "PERSONAL_GENDER": ?>
                        <div class="modal-field">
                            <h4><?=GetMessage("REGISTER_FIELD_PERSONAL_GENDER")?></h4>
                            <div id="gender-choose">
                                <div class="radio-button__variant-block">
                                    <label class="radio-button__gender-variant">
                                        <input checked class="radio-input__gender__radio-button" id="male" name="REGISTER[<?=$FIELD?>]"
                                               value="M" type="radio"/>
                                        <span class="radio-male"><?=GetMessage("USER_MALE")?></span>
                                    </label>
                                    <label class="radio-button__gender-variant">
                                        <input class="radio-input__gender__radio-button" id="female" name="REGISTER[<?=$FIELD?>]"
                                               value="F" type="radio"/>
                                        <span class="radio-female"><?=GetMessage("USER_FEMALE")?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <? break; ?>
                    <? case "PERSONAL_PHONE": ?>
                        <div class="modal-field">
                            <label for="reg-phone"><span class="required-sign">*</span><?=GetMessage("REGISTER_FIELD_PERSONAL_PHONE")?></label>
                            <input required name="REGISTER[<?= $FIELD ?>]" id="reg-phone" placeholder="+ 7 (ХХХ) ХХХ ХХ ХХ"/>
                        </div>
                    <? break; ?>
                    <? case "EMAIL": ?>
                        <div class="modal-field">
                            <label for="registerEmail"><span class="required-sign">*</span><?=GetMessage("REGISTER_FIELD_EMAIL")?></label>
                            <input pattern="^\S+@\S+\.\S+$" required type="email" id="registerEmail" placeholder="abc_./@email.ru" name="REGISTER[<?= $FIELD ?>]"/>
                        </div>
                    <? break; ?>
                    <? endswitch; ?>
                <? endforeach; ?>

                <div id="form-register-error" class="form-error-message"><?= $arResult['ERRORS'][0] ?></div>

                <p class="required-field-annotation"><span class="required-sign">*</span> поля обязательные к заполнению
                </p>
                <?php
                $consentIdPrefix = 'registration';
                include $_SERVER['DOCUMENT_ROOT'] . '/include/consent-checkboxes.php';
                ?>
                <div class="modal__choose-block">
                    <a href="#">
                        <input type="hidden" name="register_submit_button" value="<?=GetMessage("AUTH_REGISTER_ACTION")?>" />
                        <button type="submit" id="submit" class="modal-submit-button"><?=GetMessage("AUTH_REGISTER_ACTION")?></button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</dialog>
