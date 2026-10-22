<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
?>
<!--Смена пароля-->
<dialog id="changePasswordDialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button  onclick="closeDialog(document.getElementById('changePasswordDialog'))">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="change-password">
        <div class="modal-content">
            <h1>Новый пароль</h1>
            <p>Придумайте новый пароль. Пароль может содержать латинские буквы и символы _ / . @</p>
        </div>
        <form class="content" id="changePasswordForm" method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">
            <div class="change-password__form">
                <div class="modal-field">
                    <input type="hidden" name="AUTH_FORM" value="Y">
                    <input type="hidden" name="TYPE" value="CHANGE_PWD">
                    <input type="hidden" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input" autocomplete="off" />
                    <input type="hidden" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="bx-auth-input" />

                    <label for="newPassword"><span class="required-sign">*</span>Новый пароль</label>
                    <input
                            name="USER_PASSWORD"
                            autocomplete="password"
                            id="new-password-input"
                            class=""
                            required
                            minlength="8"
                            type="password"
                            placeholder="••••••••"
                    />
                    <svg class="password-control" data-target="new-password-input" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1_6154)">
                            <path d="M9.9 4.24002C10.5883 4.0789 11.2931 3.99836 12 4.00003C19 4.00003 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19M14.12 14.12C13.8454 14.4148 13.5141 14.6512 13.1462 14.8151C12.7782 14.9791 12.3809 15.0673 11.9781 15.0744C11.5753 15.0815 11.1752 15.0074 10.8016 14.8565C10.4281 14.7056 10.0887 14.4811 9.80385 14.1962C9.51897 13.9113 9.29439 13.572 9.14351 13.1984C8.99262 12.8249 8.91853 12.4247 8.92563 12.0219C8.93274 11.6191 9.02091 11.2219 9.18488 10.8539C9.34884 10.4859 9.58525 10.1547 9.88 9.88003M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 3.96914 7.65663 6.06 6.06003L17.94 17.94Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1 1L23 23" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_1_6154">
                                <rect width="24" height="24" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span id="password-error" class="form-error-message"></span>
                </div>
                <div class="modal-field">
                    <label for="repeatPassword"><span class="required-sign">*</span>Подтвердите пароль</label>
                    <input
                            name="USER_CONFIRM_PASSWORD"
                            autocomplete=""
                            id="repeat-password-input"
                            class=""
                            required
                            minlength="8"
                            type="password"
                            placeholder="••••••••"
                    />
                    <svg class="password-control" data-target="repeat-password-input" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1_6154)">
                            <path d="M9.9 4.24002C10.5883 4.0789 11.2931 3.99836 12 4.00003C19 4.00003 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19M14.12 14.12C13.8454 14.4148 13.5141 14.6512 13.1462 14.8151C12.7782 14.9791 12.3809 15.0673 11.9781 15.0744C11.5753 15.0815 11.1752 15.0074 10.8016 14.8565C10.4281 14.7056 10.0887 14.4811 9.80385 14.1962C9.51897 13.9113 9.29439 13.572 9.14351 13.1984C8.99262 12.8249 8.91853 12.4247 8.92563 12.0219C8.93274 11.6191 9.02091 11.2219 9.18488 10.8539C9.34884 10.4859 9.58525 10.1547 9.88 9.88003M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 3.96914 7.65663 6.06 6.06003L17.94 17.94Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1 1L23 23" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_1_6154">
                                <rect width="24" height="24" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span id="newPassword-error" class="form-error-message"></span>
                </div>
                <p class="form-note required"><span class="required-sign">*</span>поля обязательные к заполнению</p>
                <div class="modal__choose-block">
                    <a href="#">
                        <button type="submit" id="submitChangePassword" class="modal-submit-button" name="change_pwd">Сохранить</button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</dialog>
<!--Успешная отправка "Смена пароля"-->
<dialog id="changePasswordSuccess" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button  onclick="closeDialog(document.getElementById('changePasswordSuccess'))">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="change-password">
        <div class="modal-content">
            <h1>Готово</h1>
            <p>Пароль изменен</p>
        </div>
    </div>
</dialog>