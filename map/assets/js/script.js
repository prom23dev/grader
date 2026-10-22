const regionCodes = {
    "Республика Адыгея": "01",
    "Республика Башкортостан": "02",
    "Республика Бурятия": "03",
    "Республика Алтай": "04",
    "Республика Дагестан": "05",
    "Республика Ингушетия": "06",
    "Кабардино-Балкарская республика": "07",
    "Республика Калмыкия": "08",
    "Карачаево-Черкесская республика": "09",
    "Республика Карелия": "10",
    "Республика Коми": "11",
    "Республика Марий Эл": "12",
    "Республика Мордовия": "13",
    "Республика Саха (Якутия)": "14",
    "Республика Северная Осетия - Алания": "15",
    "Республика Татарстан": "16",
    "Республика Тыва": "17",
    "Удмуртская республика": "18",
    "Республика Хакасия": "19",
    "Чеченская республика": "20",
    "Чувашская республика": "21",
    "Алтайский край": "22",
    "Краснодарский край": "23",
    "Красноярский край": "24",
    "Приморский край": "25",
    "Ставропольский край": "26",
    "Хабаровский край": "27",
    "Амурская область": "28",
    "Архангельская область": "29",
    "Астраханская область": "30",
    "Белгородская область": "31",
    "Брянская область": "32",
    "Владимирская область": "33",
    "Волгоградская область": "34",
    "Вологодская область": "35",
    "Воронежская область": "36",
    "Ивановская область": "37",
    "Иркутская область": "38",
    "Калининградская область": "39",
    "Калужская область": "40",
    "Камчатский край": "41",
    "Кемеровская область": "42",
    "Кировская область": "43",
    "Костромская область": "44",
    "Курганская область": "45",
    "Курская область": "46",
    "Ленинградская область": "47",
    "Липецкая область": "48",
    "Магаданская область": "49",
    "Московская область": "50",
    "Мурманская область": "51",
    "Нижегородская область": "52",
    "Новгородская область": "53",
    "Новосибирская область": "54",
    "Омская область": "55",
    "Оренбургская область": "56",
    "Орловская область": "57",
    "Пензенская область": "58",
    "Пермский край": "59",
    "Псковская область": "60",
    "Ростовская область": "61",
    "Рязанская область": "62",
    "Самарская область": "63",
    "Саратовская область": "64",
    "Сахалинская область": "65",
    "Свердловская область": "66",
    "Смоленская область": "67",
    "Тамбовская область": "68",
    "Тверская область": "69",
    "Томская область": "70",
    "Тульская область": "71",
    "Тюменская область": "72",
    "Ульяновская область": "73",
    "Челябинская область": "74",
    "Забайкальский край": "75",
    "Ярославская область": "76",
    "Москва": "77",
    "Санкт-Петербург": "78",
    "Еврейская автономная область": "79",
    "Ненецкий автономный округ": "83",
    "Ханты-Мансийский автономный округ - Югра": "86",
    "Чукотский автономный округ": "87",
    "Ямало-Ненецкий автономный округ": "89",
    "Республика Крым": "91",
    "Севастополь": "92",
    "Байконур": "94"
};

let regionsData = [];

// ГЛОБАЛЬНЫЕ ПЕРЕМЕННЫЕ ДЛЯ ХРАНЕНИЯ ВЫБРАННЫХ ЗНАЧЕНИЙ
// Фильтрация проектов (getProjects) ведётся по ID элементов HL-блоков
// GeoRegion/GeoDistrict/GeoSettlement, т.к. свойства проекта PR_LOCATION*
// хранят эти же ID (см. local/php_interface/geoLocation.php).
let selectedRegion = '';
let selectedRegionId = '';
let selectedDistrict = '';
let selectedDistrictId = '';
let selectedSettlement = '';
let selectedSettlementId = '';

function isMobile() {
    return window.innerWidth <= 900;
}

function scrollToElement(element, delay = 200) {
    if (!isMobile()) {
        return;
    }
    setTimeout(() => {
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, delay);
}

// Загрузка регионов из HL-блока GeoRegion + запуск вывода областей
fetch('/map/get-geo.php?type=regions')
    .then(response => response.json())
    .then(data => {
        regionsData = data;
        renderRegions();
    })
    .catch(error => {
        console.error('Ошибка загрузки регионов:', error);
    });

// 1. Вывод областей с обработчиком клика
function renderRegions() {
    const regionsDiv = document.getElementById('regions');
    let html = '<ul>';
    regionsData.forEach(region => {
        const code = regionCodes[region.name]
            ? `<span>[${regionCodes[region.name]}]</span>`
            : '';
        html += `<li class="region-item" data-region-id="${region.id}" data-region="${region.name}">${code}${region.name}</li>`;
    });
    html += '</ul>';
    regionsDiv.innerHTML = html;

    document.querySelectorAll('.region-item').forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('active')) {
                return;
            }

            document.querySelectorAll('.region-item').forEach(r => r.classList.remove('active'));
            this.classList.add('active');

            const regionId = this.dataset.regionId;
            const regionName = this.dataset.region;

            // Сохраняем выбранный регион
            selectedRegion = regionName;
            selectedRegionId = regionId;
            selectedDistrict = '';
            selectedDistrictId = '';
            selectedSettlement = '';
            selectedSettlementId = '';

            renderDistricts(regionId, regionName);
            getProjects(selectedRegionId, selectedDistrictId, selectedSettlementId);

            const districtsDiv = document.getElementById('districts');
            const districtsWrapper = districtsDiv ? districtsDiv.closest('.map-container-wrapper') : null;
            scrollToElement(districtsWrapper);
        });
    });

    document.getElementById('districts').innerHTML = '';
    document.getElementById('settlements').innerHTML = '';
    document.getElementById('projects').innerHTML = '';
}

// 2. Вывод районов выбранного региона
function renderDistricts(regionId, regionName) {
    const districtsDiv = document.getElementById('districts');
    const districtsWrapper = districtsDiv.closest('.map-container-wrapper');

    let title = districtsWrapper.querySelector('.map-container-title');
    if (!title) {
        title = document.createElement('h3');
        title.className = 'map-container-title';
        title.textContent = 'Район';
        districtsWrapper.insertBefore(title, districtsDiv);
    }

    districtsDiv.innerHTML = '';
    document.getElementById('settlements').innerHTML = '';
    document.getElementById('projects').innerHTML = '';

    fetch('/map/get-geo.php?type=districts&region_id=' + encodeURIComponent(regionId))
        .then(response => response.json())
        .then(districts => {
            let html = '<ul>';
            districts.forEach(distr => {
                html += `<li class="district-item" data-district-id="${distr.id}" data-region="${regionName}" data-district="${distr.name}">${distr.name}</li>`;
            });
            html += '</ul>';
            districtsDiv.innerHTML = html;

            // Назначаем обработчик клика для районов
            document.querySelectorAll('.district-item').forEach(item => {
                item.addEventListener('click', function() {
                    if (this.classList.contains('active')) {
                        return;
                    }

                    document.querySelectorAll('.district-item').forEach(d => d.classList.remove('active'));
                    this.classList.add('active');

                    const districtId = this.dataset.districtId;
                    const districtName = this.dataset.district;

                    // Сохраняем выбранный район
                    selectedDistrict = districtName;
                    selectedDistrictId = districtId;
                    selectedSettlement = '';
                    selectedSettlementId = '';

                    renderSettlements(districtId);
                    getProjects(selectedRegionId, selectedDistrictId, selectedSettlementId);

                    const settlementsDiv = document.getElementById('settlements');
                    const settlementsWrapper = settlementsDiv ? settlementsDiv.closest('.map-container-wrapper') : null;
                    scrollToElement(settlementsWrapper);
                });
            });
        })
        .catch(error => {
            console.error('Ошибка загрузки районов:', error);
        });
}

// 3. Вывод населённых пунктов выбранного района
function renderSettlements(districtId) {
    const settlementsDiv = document.getElementById('settlements');
    const settlementsWrapper = settlementsDiv.closest('.map-container-wrapper');

    let title = settlementsWrapper.querySelector('.map-container-title');
    if (!title) {
        title = document.createElement('h3');
        title.className = 'map-container-title';
        title.textContent = 'Населенный пункт';
        settlementsWrapper.insertBefore(title, settlementsDiv);
    }

    settlementsDiv.innerHTML = `
        <div class="settlement-custom-input">
            <input type="text"
                   id="custom-settlement-input"
                   class="custom-settlement-input"
                   placeholder="Введите название населенного пункта">
            <button type="button"
                    id="custom-settlement-button"
                    class="custom-settlement-button">Добавить</button>
        </div>
        <ul></ul>
    `;
    document.getElementById('projects').innerHTML = '';

    fetch('/map/get-geo.php?type=settlements&district_id=' + encodeURIComponent(districtId))
        .then(response => response.json())
        .then(settlements => {
            const ul = settlementsDiv.querySelector('ul');
            let html = '';
            settlements.forEach(settl => {
                html += `<li class="settlement-item" data-settlement-id="${settl.id}" data-settlement="${settl.name}">${settl.name}</li>`;
            });
            ul.innerHTML = html;

            // Назначаем обработчик клика для населённых пунктов
            document.querySelectorAll('.settlement-item').forEach(item => {
                item.addEventListener('click', function() {
                    if (this.classList.contains('active')) {
                        return;
                    }

                    document.querySelectorAll('.settlement-item').forEach(s => s.classList.remove('active'));
                    this.classList.add('active');

                    // Сохраняем выбранный населенный пункт
                    selectedSettlement = this.dataset.settlement;
                    selectedSettlementId = this.dataset.settlementId;

                    // ОТПРАВЛЯЕМ ВСЕ ТРИ ID
                    getProjects(selectedRegionId, selectedDistrictId, selectedSettlementId);
                });
            });

            initCustomSettlementInput();
        })
        .catch(error => {
            console.error('Ошибка загрузки населённых пунктов:', error);
        });
}

// 4. AJAX запрос за проектами (региона/района/НП передаются ID элементов HL-блоков)
function getProjects(regionId = '', districtId = '', settlementId = '') {
    const projectsDiv = document.getElementById('projects');

    // Показываем лоадер
    showLoader(projectsDiv);

    fetch('/map/get-project.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            region: regionId,
            district: districtId,
            settlement: settlementId
        })
    })
        .then(r => r.text())
        .then(html => {
            hideLoader(projectsDiv);
            const shouldScroll = settlementId !== '';
            renderProjects(html, shouldScroll);
        })
        .catch(error => {
            hideLoader(projectsDiv);
            console.error('Ошибка загрузки проектов:', error);
            projectsDiv.innerHTML = `<div class="no-projects">
                        <h1>Ошибка загрузки проектов.</h1>
                    </div>`;
        });
}

// Показать лоадер
function showLoader(container) {
    container.innerHTML = `
        <div class="projects-loader">
            <div class="loader-spinner"></div>
        </div>
    `;
}

// Скрыть лоадер
function hideLoader(container) {
    const loader = container.querySelector('.projects-loader');
    if (loader) {
        loader.remove();
    }
}

// 5. Отрисовка списка проектов
function renderProjects(html, shouldScroll = false) {
    const projectsDiv = document.getElementById('projects');
    if (!html || html.trim() === '') {
        projectsDiv.innerHTML = `<div class="no-projects">
                    <h1>Проекты не найдены.</h1>
                </div>`;
        // Скроллить только если shouldScroll = true (т.е. выбран населенный пункт)
        if (shouldScroll && isMobile() && projectsDiv) {
            setTimeout(() => {
                projectsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    } else {
        projectsDiv.innerHTML = html;

        setTimeout(() => {
            const ongoingLists = projectsDiv.querySelectorAll('.projects__list');
            ongoingLists.forEach(list => {
                const items = list.querySelectorAll('.projects__list-item');
                if (items.length === 1) {
                    list.classList.add('single-item');
                } else {
                    list.classList.remove('single-item');
                }
            });
            
            const completedLists = projectsDiv.querySelectorAll('.projects__list');
            completedLists.forEach(list => {
                const items = list.querySelectorAll('.projects__list-item');
                if (items.length === 1) {
                    list.classList.add('single-item');
                } else {
                    list.classList.remove('single-item');
                }
            });

            // Скроллить только если shouldScroll = true (т.е. выбран населенный пункт)
            if (shouldScroll && isMobile() && projectsDiv) {
                setTimeout(() => {
                    projectsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        }, 100);
    }
}


