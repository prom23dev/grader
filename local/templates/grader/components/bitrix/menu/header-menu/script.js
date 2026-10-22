    function toggleMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('active');
    }

    function getCityButtons() {
        return [document.getElementById('headerCityBtn'), document.getElementById('headerCityBtnMobile')].filter(Boolean);
    }

    function positionCityPopup() {
        const popup = document.getElementById('headerCityPopup');
        const btn = getCityButtons()[0];
        if (!popup || !btn) return;
        const rect = btn.getBoundingClientRect();
        const isMobile = window.matchMedia('(max-width: 765px)').matches;
        popup.style.top = (rect.bottom + 8) + 'px';
        if (isMobile) {
            popup.style.left = '50%';
            popup.style.transform = 'translateX(-50%)';
        } else {
            popup.style.left = rect.left + 'px';
            popup.style.transform = 'none';
        }
    }

    function toggleCityPopup() {
        const popup = document.getElementById('headerCityPopup');
        const isOpen = popup.classList.toggle('header__city-popup--open');
        getCityButtons().forEach(function (btn) {
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        if (isOpen) {
            positionCityPopup();
            const search = popup.querySelector('.header__city-popup-search');
            if (search) search.focus();
        }
    }

    window.addEventListener('resize', function () {
        const popup = document.getElementById('headerCityPopup');
        if (popup && popup.classList.contains('header__city-popup--open')) positionCityPopup();
    });

    function setSelectedCityCookie(id) {
        const expires = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toUTCString();
        document.cookie = 'SELECTED_CITY_ID=' + encodeURIComponent(id) + '; expires=' + expires + '; path=/';
    }

    function getCityDisplayName(name) {
        return String(name || '').replace(/\s*\([^)]*\)\s*$/, '');
    }

    function selectCity(id, name) {
        document.querySelectorAll('.header__city-name').forEach(function (span) {
            span.textContent = getCityDisplayName(name);
        });
        const popup = document.getElementById('headerCityPopup');
        if (popup) popup.dataset.selectedCityId = String(id);
        setSelectedCityCookie(id);
        window.location.reload();
    }

    function renderCityOptions(items) {
        const list = document.getElementById('headerCityPopupList');
        if (!list) return;
        const popup = document.getElementById('headerCityPopup');
        const selectedId = popup ? popup.dataset.selectedCityId : '';

        if (!items.length) {
            list.innerHTML = '<p class="header__city-popup-hint">Ничего не найдено</p>';
            return;
        }

        list.innerHTML = '';
        items.forEach(function (item) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'header__city-item-option' + (String(item.id) === selectedId ? ' header__city-item-option--active' : '');
            btn.textContent = item.name;
            btn.addEventListener('click', function () {
                list.querySelectorAll('.header__city-item-option').forEach(function (el) {
                    el.classList.remove('header__city-item-option--active');
                });
                btn.classList.add('header__city-item-option--active');
                selectCity(item.id, item.name);
            });
            list.appendChild(btn);
        });
    }

    let citySearchTimer = null;
    let citySearchXhr = null;

    function searchCities(query) {
        const list = document.getElementById('headerCityPopupList');
        if (!list) return;

        const trimmed = query.trim();
        if (!trimmed) {
            list.innerHTML = '<p class="header__city-popup-hint">Введите название населённого пункта</p>';
            return;
        }

        if (citySearchXhr) citySearchXhr.abort();

        citySearchXhr = new XMLHttpRequest();
        citySearchXhr.open('GET', '/header/search-city.php?q=' + encodeURIComponent(trimmed), true);
        citySearchXhr.onload = function () {
            if (citySearchXhr.status !== 200) return;
            try {
                renderCityOptions(JSON.parse(citySearchXhr.responseText));
            } catch (e) {
                // игнорируем некорректный ответ
            }
        };
        citySearchXhr.send();
    }

    document.addEventListener('input', function (event) {
        if (!event.target.classList || !event.target.classList.contains('header__city-popup-search')) return;
        const value = event.target.value;
        if (citySearchTimer) clearTimeout(citySearchTimer);
        citySearchTimer = setTimeout(function () {
            searchCities(value);
        }, 300);
    });

    document.addEventListener('click', function (event) {
        const popup = document.getElementById('headerCityPopup');
        if (!popup || !popup.classList.contains('header__city-popup--open')) return;
        const buttons = getCityButtons();
        if (popup.contains(event.target) || buttons.some(function (btn) { return btn.contains(event.target); })) return;
        toggleCityPopup();
    });

    (function () {
        const confirmBox = document.getElementById('headerCityConfirm');
        if (!confirmBox) return;

        const yesBtn = document.getElementById('headerCityConfirmYes');
        const noBtn = document.getElementById('headerCityConfirmNo');

        if (yesBtn) {
            yesBtn.addEventListener('click', function () {
                setSelectedCityCookie(confirmBox.dataset.cityId);
                confirmBox.remove();
            });
        }

        if (noBtn) {
            noBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                confirmBox.remove();
                toggleCityPopup();
            });
        }
    })();

    // Если GeoIP не смог определить город, запрашиваем разрешение браузера
    // на геолокацию. При отказе показываем безопасный вариант по умолчанию.
    (function requestLocationOrFallbackCity() {
        if (document.cookie.indexOf('SELECTED_CITY_ID=') !== -1) return;
        if (document.getElementById('headerCityConfirm')) return;

        function showMoscowFallback() {
            if (sessionStorage.getItem('graderCityFallbackShown') === '1') return;
            sessionStorage.setItem('graderCityFallbackShown', '1');
            const box = document.createElement('div');
            box.className = 'header__city-confirm';
            box.innerHTML = '<p class="header__city-confirm-text">Ваш город — «Москва»?</p>' +
                '<div class="header__city-confirm-actions">' +
                '<button type="button" class="header__city-confirm-btn header__city-confirm-btn--yes">Да</button>' +
                '<button type="button" class="header__city-confirm-btn header__city-confirm-btn--no">Нет</button></div>';
            document.body.appendChild(box);
            box.querySelector('.header__city-confirm-btn--yes').addEventListener('click', function () {
                setSelectedCityCookie('0');
                box.remove();
            });
            box.querySelector('.header__city-confirm-btn--no').addEventListener('click', function () {
                box.remove();
                toggleCityPopup();
            });
        }

        if (!navigator.geolocation) {
            showMoscowFallback();
            return;
        }
        navigator.geolocation.getCurrentPosition(function () {}, showMoscowFallback, {
            enableHighAccuracy: false,
            timeout: 5000,
            maximumAge: 300000
        });
    })();
