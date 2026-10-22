<?php
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_DIR . "front/pages/account/settings/css/styles.css");
global $USER;
$settingsUserFields = \CUser::GetByID($USER->GetID())->Fetch();
$emailNotifyEnabled = !isset($settingsUserFields['UF_EMAIL_NOTIFY']) || (int)$settingsUserFields['UF_EMAIL_NOTIFY'] === 1;
?>
<section class="profile">
    <div class="">
        <h1 id="main-settings-title">Настройки</h1>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/personal/profile-tabs.php'; ?>


        <nav class="settings__choose-bar" role="tablist" aria-label="Переключение разделов настроек">
            <button id="notifications" class="active" role="tab" aria-selected="true"
                    aria-controls="settings__notification__mailing">
                Уведомления
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M17.5608 13.0608L10.0608 20.5608L7.93945 18.4395L14.3788 12.0001L7.93945 5.56077L10.0608 3.43945L17.5608 10.9395C18.1466 11.5252 18.1466 12.475 17.5608 13.0608Z"
                          fill="#474A51"/>
                </svg>
            </button>
            <button id="changePassword" class="" role="tab" aria-selected="false"
                    aria-controls="settings__password-change">
                Сменить пароль
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M17.5608 13.0608L10.0608 20.5608L7.93945 18.4395L14.3788 12.0001L7.93945 5.56077L10.0608 3.43945L17.5608 10.9395C18.1466 11.5252 18.1466 12.475 17.5608 13.0608Z"
                          fill="#474A51"/>
                </svg>
            </button>
            <button id="deleteProfile" class="" role="tab" aria-selected="false"
                    aria-controls="settings__delete-profile">
                Удалить профиль
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M17.5608 13.0608L10.0608 20.5608L7.93945 18.4395L14.3788 12.0001L7.93945 5.56077L10.0608 3.43945L17.5608 10.9395C18.1466 11.5252 18.1466 12.475 17.5608 13.0608Z"
                          fill="#474A51"/>
                </svg>
            </button>
        </nav>
        <!--  Блок для "Уведомлений"-->
        <div id="settings__notification__mailing">
            <ul>
                <li>
                    <label class="switch" for="email-notifications">
                        <input type="checkbox" id="email-notifications" data-sessid="<?=bitrix_sessid()?>"<?= $emailNotifyEnabled ? ' checked' : '' ?>/>
                        Получать e-mail рассылку
                        <span class="slider round"></span>
                    </label>
                </li>
                <!--<li>
                    <label class="switch" for="push-notifications">
                        <input type="checkbox" id="push-notifications"/>
                        Получать Push-уведомления
                        <span class="slider round"></span>
                    </label>
                </li>-->
            </ul>
        </div>
        <!--  Блок для "Смены пароля"-->
        <div id="settings__password-change" class="settings__password-change--hidden">
            <form class="change-password-form">
                <label class="form-label">
                    <span class="label-text required">Старый пароль</span>
                    <input required id="old-password-input" placeholder="••••••••" type="password"/>
                    <svg class="password-control" data-target="old-password-input" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <span id="old-password-error" class="form-error-message"></span>
                </label>
                <span class="password-hint">Придумайте новый пароль. Пароль может содержать латинские буквы и символы _ / . @</span>
                <label class="form-label">
                    <span class="label-text required">Новый пароль</span>
                    <input
                            required
                            pattern="^[a-zA-Z_/.@]{8,16}$"
                            minlength="8" id="new-password-input"
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
                    <span id="new-password-error" class="form-error-message"></span>
                </label>
                <label class="form-label">
                    <span class="label-text required">Подтвердите новый пароль</span>
                    <input
                            required
                            pattern="^[a-zA-Z_/.@]{8,16}$"
                            minlength="8"
                            id="agree-password-input"
                            type="password"
                            placeholder="••••••••"
                    />
                    <svg class="password-control" data-target="agree-password-input" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <span id="agree-password-error" class="form-error-message"></span>
                </label>
                <p class="form-note required">поля обязательные к заполнению</p>
                <div class="settings__password__save-password">
                    <button type="submit" name="submit" id="submit" class="settings__password___save-password-button">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
        <!--  Блок для "Удаления профиля"-->
        <div id="settings__delete-profile" class="settings__delete-profile--hidden">
            <ul class="account__menu account__menu--actions profile-actions">
                <li class="account__menu-item account__delete-profile">
                    <a href="#" id="deleteProfileLink" data-sessid="<?=bitrix_sessid()?>">
                        <span>Удалить профиль</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 4H8L1 12L8 20H21C21.5304 20 22.0391 19.7893 22.4142 19.4142C22.7893 19.0391 23 18.5304 23 18V6C23 5.46957 22.7893 4.96086 22.4142 4.58579C22.0391 4.21071 21.5304 4 21 4V4Z" stroke="#FF4000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 9L12 15" stroke="#FF4000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 9L18 15" stroke="#FF4000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</section>

<dialog class="dialog" id="deleteDialog">
    <form method="dialog">
        <div class="delete-dialog__content">
            <h1>Удаление личного кабинета</h1>
            <div>
                <p>Все сохранённые данные о проектах будут безвозвратно утрачены. Вы действительно хотите удалить личный кабинет?</p>
            </div>
            <div class="delete-dialog__choose-block">
                <button class="dialog-agree-button" id="deleteYes" type="button">Да</button>
                <button class="dialog-disagree-button" id="deleteNo" type="button">Нет</button>
            </div>
        </div>
    </form>
</dialog>
<dialog class="dialog" id="deleteSuccessDialog">
    <form method="dialog">
        <div class="delete-dialog__content">
            <p id="delete-success__message">Личный кабинет успешно удалён</p>
            <div class="delete-success__dialog__content">
                <button class="delete-success__go-to-main" id="deleteSuccessYes" type="button">На главную</button>
            </div>
        </div>
    </form>
</dialog>

<dialog class="dialog" id="success-change-dialog">
    <form method="dialog">
        <div class="closeModal">
            <button>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.1213 11.8495L20.4852 5.48558L18.3639 3.36426L11.9999 9.72822L5.63598 3.36426L3.51466 5.48558L9.87862 11.8495L3.51465 18.2135L5.63597 20.3348L11.9999 13.9709L18.3639 20.3348L20.4852 18.2135L14.1213 11.8495Z" fill="#474A51"/>
                </svg>
            </button>
        </div>
        <div class="success-change-dialog__content">
            <h1>Ваш пароль успешно изменен. </h1>
            <p>Мы рекомендуем никому не передавать его и хранить в надежном месте.</p>
            <div>
                <button>
                    Закрыть
                </button>
            </div>

        </div>

    </form>
</dialog>
<script>
    $(document).ready(function () {
        const emailNotifyCheckbox = document.getElementById('email-notifications');
        if (emailNotifyCheckbox) {
            emailNotifyCheckbox.addEventListener('change', function () {
                const checkbox = this;
                const previousValue = !checkbox.checked;
                checkbox.disabled = true;
                $.post("/local/templates/grader/assets/ajax/saveEmailNotify.php", {
                    enabled: checkbox.checked ? '1' : '0',
                    sessid: checkbox.dataset.sessid,
                })
                    .done(function (response) {
                        if (!response || !response.success) {
                            checkbox.checked = previousValue;
                        }
                    })
                    .fail(function () {
                        checkbox.checked = previousValue;
                    })
                    .always(function () {
                        checkbox.disabled = false;
                    });
            });
        }

        const passwordControls = document.querySelectorAll('.password-control');
        passwordControls.forEach(control => {
            const targetId = control.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);

            control.addEventListener('click', () => {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';  // Показываем пароль
                    control.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`


                } else {
                    targetInput.type = 'password';  // Скрываем пароль
                    control.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1_6154)">
                        <path d="M9.9 4.24002C10.5883 4.0789 11.2931 3.99836 12 4.00003C19 4.00003 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19M14.12 14.12C13.8454 14.4148 13.5141 14.6512 13.1462 14.8151C12.7782 14.9791 12.3809 15.0673 11.9781 15.0744C11.5753 15.0815 11.1752 15.0074 10.8016 14.8565C10.4281 14.7056 10.0887 14.4811 9.80385 14.1962C9.51897 13.9113 9.29439 13.572 9.14351 13.1984C8.99262 12.8249 8.91853 12.4247 8.92563 12.0219C8.93274 11.6191 9.02091 11.2219 9.18488 10.8539C9.34884 10.4859 9.58525 10.1547 9.88 9.88003M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 3.96914 7.65663 6.06 6.06003L17.94 17.94Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1 1L23 23" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_1_6154">
                            <rect width="24" height="24" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>`

                }
            });
        });

        const dialogChange = document.getElementById('success-change-dialog');
        const oldPasswordInput = $('#old-password-input');
        const newPasswordInput = $('#new-password-input')
        const agreePasswordInput = $('#agree-password-input')

        const oldPasswordError = $('#old-password-error')
        const newPasswordError = $('#new-password-error')
        const agreePasswordError = $('#agree-password-error')
        const submitBtn = $('#submit');

        submitBtn.on('click', function (e) {
            e.preventDefault()
            let isValid = true;

            [oldPasswordInput, newPasswordInput, agreePasswordInput].forEach(input => {
                if (input.hasClass('error')) {
                    input.removeClass('error');
                }
            });
            [oldPasswordError, newPasswordError, agreePasswordError].forEach(error => {
                error.text('')
            });

            if (oldPasswordInput.val() === newPasswordInput.val()) {
                newPasswordInput.addClass('error');
                newPasswordError.text('Новый пароль не должен совпадать со старым.');
                newPasswordError.show()
                isValid = false;
            }
            if (newPasswordInput.val() !== agreePasswordInput.val()) {
                agreePasswordInput.addClass('error');
                agreePasswordError.text('Пароли не совпадают.');
                agreePasswordError.show()
                isValid = false;
            }
            if (isValid) {
                $.post("/local/templates/grader/assets/ajax/changePassword.php", {
                        oldPassword: $('#old-password-input').val(),
                        newPassword: $('#new-password-input').val(),
                        agreePassword: $('#agree-password-input').val()
                    },
                    function (response) {
                        if (response) {
                            console.log('old password TRUE')
                            dialogChange.showModal();
                            dialogChange.classList.add('show');
                            dialogChange.addEventListener('close',() =>{
                                window.location.reload();
                            });
                        } else {
                            console.log('old password FALSE')
                            oldPasswordError.text('Введен неверный старый пароль.')
                        }
                    })
            }
        })

        const mainTitle = document.getElementById('main-settings-title')
        const settingsChooseBar = document.querySelector('.settings__choose-bar')
        const backButton = document.querySelector('.breadcrumb-current-page-mobile a');

        const tabs = [
            {
                tab: document.getElementById('notifications'),
                block: document.getElementById('settings__notification__mailing'),
                key: 'notifications',
                mobileTitle: 'Настройки уведомлений',
            },
            {
                tab: document.getElementById('changePassword'),
                block: document.getElementById('settings__password-change'),
                key: 'password',
                mobileTitle: 'Сменить пароль',
            },
            {
                tab: document.getElementById('deleteProfile'),
                block: document.getElementById('settings__delete-profile'),
                key: 'deleteProfile',
                mobileTitle: 'Удалить профиль',
            },
        ];

        let activeTab = 'notifications';

        let isSubMenu = false;

        // Функция для переключения видимости блоков
        function switchTab(selected) {
            tabs.forEach(({tab, block}) => {
                const isSelected = tab === selected.tab;
                tab.classList.toggle('active', isSelected);
                tab.setAttribute('aria-selected', String(isSelected));
                block.style.display = isSelected ? 'block' : 'none';
            });

            activeTab = selected.key;

            if (window.innerWidth < 766) {
                mainTitle.textContent = selected.mobileTitle;
                mainTitle.style.fontSize = '22px';
                settingsChooseBar.style.display = 'none';
                isSubMenu = true;
                if (backButton) {
                    backButton.setAttribute('href', '#');
                }
            } else {
                settingsChooseBar.style.display = 'flex';
            }
        }

        // Переключение между вкладками настроек
        tabs.forEach((entry) => {
            entry.tab.addEventListener('click', () => switchTab(entry));
        });

        // Обработчик события на кнопку "Назад" в мобильной версии
        if (backButton) {
            backButton.addEventListener('click', (e) => {
                if (window.innerWidth < 766 && isSubMenu) {
                    e.preventDefault(); // Останавливаем стандартное поведение перехода
                    mainTitle.textContent = 'Настройки'; // Возвращаем заголовок
                    mainTitle.style.fontSize = '28px';
                    settingsChooseBar.style.display = 'flex';
                    tabs.forEach(({block}) => {
                        block.style.display = 'none';
                    });
                    isSubMenu = false;
                    backButton.setAttribute('href', '/front/pages/account/')

                }
            });
        }

        // Изначально показываем блок активной вкладки, остальные скрываем
        function initializeView() {
            if (window.innerWidth < 766) {
                // В мобильной версии скрываем все блоки
                tabs.forEach(({block}) => {
                    block.style.display = 'none';
                });
                mainTitle.textContent = 'Настройки';
                settingsChooseBar.style.display = 'flex';
            } else {
                // В десктопной версии отображаем активный блок
                tabs.forEach(({block, key}) => {
                    block.style.display = key === activeTab ? 'block' : 'none';
                });
            }
        }

        initializeView();
        window.addEventListener('resize', initializeView);

        const deleteLink = document.getElementById('deleteProfileLink');
        const deleteDialog = document.getElementById('deleteDialog');
        const deleteSuccessDialog = document.getElementById('deleteSuccessDialog');
        const deleteYesButton = document.getElementById('deleteYes');
        const deleteNoButton = document.getElementById('deleteNo');
        const deleteSuccessYesButton = document.getElementById('deleteSuccessYes');

        deleteLink.addEventListener('click', (e) => {
            e.preventDefault();
            deleteDialog.showModal();
            deleteDialog.classList.add('show');
        });

        deleteNoButton.addEventListener('click', () => {
            deleteDialog.close();
            deleteDialog.classList.remove('show');
        });

        deleteYesButton.addEventListener('click', () => {
            deleteYesButton.disabled = true;
            fetch('/local/templates/grader/assets/ajax/deleteProfile.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'sessid=' + encodeURIComponent(deleteLink.dataset.sessid),
            })
                .then((response) => response.json())
                .then((data) => {
                    deleteDialog.close();
                    deleteDialog.classList.remove('show');
                    if (data.success) {
                        deleteSuccessDialog.showModal();
                        deleteSuccessDialog.classList.add('show');
                    } else {
                        deleteYesButton.disabled = false;
                    }
                });
        });

        deleteSuccessYesButton.addEventListener('click', () => {
            window.location.replace('/');
        });
    });
</script>

