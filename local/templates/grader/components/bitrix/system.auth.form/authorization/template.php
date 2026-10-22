<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 * @global CUser $USER
 */
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_DIR."auth/style.css");
CJSCore::Init();
?>

<!--Авторизация-->
<dialog id="authDialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button onclick="closeAuthDialog()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>

    <div class="auth">

        <div class="modal-content">
            <h1>Авторизация</h1>
        </div>
        <form class="content" id="authForm" name="form_auth" action="" method="post">
            <input type="hidden" name="AUTH_FORM" value="Y" />
            <input type="hidden" name="TYPE" value="AUTH" />
            <div class="auth__form">
                <p class="auth__annotation">Для входа в Личный кабинет, введите почту и пароль или зарегистрируйтесь.</p>
                <div class="modal-field">
                    <label for="authEmail"><span class="required-sign">*</span><?=GetMessage("AUTH_LOGIN")?></label>
                    <input pattern="^\S+@\S+\.\S+$" required type="email" id="authEmail" placeholder="abc_./@email.ru" name="USER_LOGIN" />
                </div>
                <div class="modal-field">
                    <label for="password"><span class="required-sign">*</span><?=GetMessage("AUTH_PASSWORD")?></label>
                    <input
                            autocomplete="password"
                            id="password"
                            class=""
                            required
                            name="USER_PASSWORD"
                            type="password"
                            placeholder="••••••••"
                    />
                    <svg class="password-control" data-target="password" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <span id="password-error" class="form-error-message"><?= $arResult['ERROR_MESSAGE']['MESSAGE'] ?></span>
                </div>

                <p class="required-field-annotation"><span class="required-sign">*</span> поля обязательные к заполнению</p>
                <div class="modal__choose-block">
                    <a href="#">
                        <input type="hidden" class="btn btn-primary" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>"/>
                        <button type="submit" id="authSubmit" class="modal-submit-button"><?=GetMessage("AUTH_LOGIN_BUTTON")?></button>
                    </a>
                    <a href="#">
                        <button type="button" onclick="toggleRegisterDialog()" class="auth__go-to-register">Зарегистрироваться</button>
                    </a>
                </div>
                <a href="#" class="auth__forgot-password">
                    <button onclick="toggleForgotPasswordDialog()" type="button">
                        Забыли пароль?
                    </button>
                </a>
            </div>
        </form>
    </div>
</dialog>
<!--Такого пользователя нет-->
<dialog id="userNotExistDialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button onclick="closeDialog(userNotExitDialog)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="user-not-exist-content">
        <p>Такой логин/email уже существует</p>
        <p>Попробуйте ещё раз. <a href="#" onclick="{closeDialog(userNotExitDialog); openDialog(registerDialog)}">
                Регистрация.</a></p>
    </div>
</dialog>

<!--Неверное имя или пароль-->
<dialog id="failedLoginDialog" class="dialog">
    <div class="close-dialog">
        <a href="#">
            <button onclick="closeDialog(failedLoginDialog)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                </svg>
            </button>
        </a>
    </div>
    <div class="user-not-exist-content">
        <p>Неверное имя пользователя или пароль.</p>
        <p>Попробуйте ещё раз или <a href="#" onclick="{closeDialog(failedLoginDialog); openDialog(registerDialog)}">
                зарегистрируйтесь</a>.</p>
    </div>
</dialog>