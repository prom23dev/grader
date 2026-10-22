$(document).ready(function() {
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps: '#sitemaps',
        index_type: 'index.html'
    });

    const passwordControls = document.querySelectorAll('.password-control');
    passwordControls.forEach(control => {
        const targetId = control.getAttribute('data-target');
        const targetInput = document.getElementById(targetId);

        control.addEventListener('click', () => {
            if (targetInput.type === 'password') {
                targetInput.type = 'text';  // Показываем пароль
                control.innerHTML =`
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


    const notificationTab = document.getElementById('notifications');
    const passwordTab = document.getElementById('changePassword');
    const notificationBlock = document.getElementById('settings__notification__mailing');
    const passwordBlock = document.getElementById('settings__password-change');
    const mainTitle = document.getElementById('main-settings-title')
    const settingsChooseBar = document.querySelector('.settings__choose-bar')
    const backButton = document.querySelector('.breadcrumb-current-page-mobile a');

    let activeTab = 'notifications';

    let isSubMenu = false;

    // Функция для переключения видимости блоков
    function switchTab(selectedTab, otherTab, selectedBlock, otherBlock, newActiveTab) {
        selectedTab.classList.add('active');
        selectedTab.setAttribute('aria-selected', 'true');
        otherTab.classList.remove('active');
        otherTab.setAttribute('aria-selected', 'false');

        selectedBlock.style.display = 'block';
        otherBlock.style.display = 'none';

        activeTab = newActiveTab;
    }


    // Переключение на блок "Уведомления" + условие для мобильной версии
    notificationTab.addEventListener('click', () => {
        switchTab(notificationTab, passwordTab, notificationBlock, passwordBlock, 'notifications');
        if(window.innerWidth < 766){
            mainTitle.textContent = 'Настройки уведомлений'
            mainTitle.style.fontSize = '22px';
            settingsChooseBar.style.display = 'none'
            isSubMenu = true;
            backButton.setAttribute('href', '#');
        }
    });

    // Переключение на блок "Смена пароля" + условие для мобильной версии
    passwordTab.addEventListener('click', () => {
        switchTab(passwordTab, notificationTab, passwordBlock, notificationBlock, 'password');
        if(window.innerWidth < 766){
            mainTitle.textContent = 'Сменить пароль'
            mainTitle.style.fontSize = '22px';
            settingsChooseBar.style.display = 'none'
            isSubMenu = true;
            backButton.setAttribute('href', '#');
        }else{
            settingsChooseBar.style.display = 'flex'
        }
    });

    // Обработчик события на кнопку "Назад" в мобильной версии
    backButton.addEventListener('click', (e) => {
        if (window.innerWidth < 766 && isSubMenu)  {
            e.preventDefault(); // Останавливаем стандартное поведение перехода
            mainTitle.textContent = 'Настройки'; // Возвращаем заголовок
            mainTitle.style.fontSize = '28px';
            settingsChooseBar.style.display = 'flex';
            notificationBlock.style.display = 'none';
            passwordBlock.style.display = 'none';
            isSubMenu = false;
            backButton.setAttribute('href', '/front/pages/account/')

        }
    });
    // Изначально показываем блок "Уведомления" и скрываем "Сменить пароль"
    function initializeView() {
        if (window.innerWidth < 766) {
            // В мобильной версии скрываем оба блока
            notificationBlock.style.display = 'none';
            passwordBlock.style.display = 'none';
            mainTitle.textContent = 'Настройки';
            settingsChooseBar.style.display = 'flex';
        } else {
            // В десктопной версии отображаем активный блок
            if (activeTab === 'notifications') {
                notificationBlock.style.display = 'block';
                passwordBlock.style.display = 'none';
            } else {
                passwordBlock.style.display = 'block';
                notificationBlock.style.display = 'none';
            }
        }
    }
    initializeView();
    window.addEventListener('resize', initializeView);

    const form = document.querySelector('.change-password-form');
    const oldPasswordInput = document.getElementById('old-password-input');
    const newPasswordInput = document.getElementById('new-password-input');
    const agreePasswordInput = document.getElementById('agree-password-input');

    const oldPasswordError = document.getElementById('old-password-error');
    const newPasswordError = document.getElementById('new-password-error');
    const agreePasswordError = document.getElementById('agree-password-error');

    form.addEventListener('submit', (event) => {
        let isValid = true;

        [oldPasswordInput, newPasswordInput, agreePasswordInput].forEach(input => {
            input.classList.remove('error');
        });
        [oldPasswordError, newPasswordError, agreePasswordError].forEach(error => {
            error.style.display = 'none';
        });

        if (oldPasswordInput.value === newPasswordInput.value) {
            newPasswordInput.classList.add('error');
            newPasswordError.textContent = 'Новый пароль не должен совпадать со старым.';
            newPasswordError.style.display = 'block';
            isValid = false;
        }
        if (newPasswordInput.value !== agreePasswordInput.value) {
            agreePasswordInput.classList.add('error');
            agreePasswordError.textContent = 'Пароли не совпадают.';
            agreePasswordError.style.display = 'block';
            isValid = false;
        }
        if (!isValid) {
            event.preventDefault();
        }
    });

});