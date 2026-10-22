<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;


if ($arResult['AUTHORIZED'])
{
	echo Loc::getMessage('MAIN_AUTH_PWD_SUCCESS');
	return;
}
?>

<!--Восстановление пароля-->
<dialog id="forgotPasswordDialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button onclick="closeForgotPasswordDialog()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5608 13.0608L10.0608 20.5608L7.93945 18.4395L14.3788 12.0001L7.93945 5.56077L10.0608 3.43945L17.5608 10.9395C18.1466 11.5252 18.1466 12.475 17.5608 13.0608Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
        <a href="#">
            <button onclick="closeForgotPasswordDialog()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="forgot-password">
        <div class="modal-content">
            <h1>Восстановление пароля</h1>
            <p>Введите адрес электронной почты, указанный при регистрации, на который будет направлен новый пароль для входа в Личный кабинет.</p>
        </div>
        <form name="bform" method="post" target="_top" id="forgotPasswordForm">
<!--        <form name="bform" method="post" target="_top" action="--><?php //= POST_FORM_ACTION_URI;?><!--" id="forgotPasswordForm">-->
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="SEND_PWD">
            <div class="forgot-password__form">
                <p class="forgot-password__annotation"><?=GetMessage("sys_forgot_pass_note_email")?></p>
                <div class="modal-field">
                    <label for="email"><span class="required-sign">*</span>Почта</label>
                    <input name="USER_EMAIL" pattern="^\S+@\S+\.\S+$" required type="email" id="email" placeholder="abc_./@email.ru" />
                    <!--                    <input type="hidden" name="USER_LOGIN">-->
                </div>

                <p class="required-field-annotation"><span class="required-sign">*</span> поля обязательные к заполнению</p>
                <div class="modal__choose-block">
                    <a href="#">
                        <input type="hidden" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
                        <button onclick="toggleRecoverCodeDialog()" type="submit" id="forgotPasswordSubmit" class="modal-submit-button"><?=GetMessage("AUTH_SEND")?></button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</dialog>
<!--Успешная отправка "Забыли пароль"-->
<dialog id="callBackSuccess" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button  onclick="closeDialog(document.getElementById('callBackSuccess'))">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="callback">
        <div class="modal-content">
            <h1>Готово!</h1>
            <p>Письмо для восстановления входа в Личный кабинет отправлено на почту</p>
        </div>
    </div>
</dialog>
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/HichemTab-tech/OTP-designer-jquery@2.3.1/dist/otpdesigner.min.js"></script>