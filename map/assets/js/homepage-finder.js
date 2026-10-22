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

let selectedRegion = '';
let selectedDistrict = '';
let selectedDistrictId = '';
let selectedSettlement = '';
let currentSettlementsData = [];

function isMobile() {
    return window.innerWidth <= 900;
}

function resetFinderSelect(selector, placeholder) {
    const $el = $(selector);
    if ($el.data('select2')) {
        $el.select2('destroy');
    }
    $el.html('').prop('disabled', true);
    $el.select2({ placeholder: placeholder, width: '100%', language: 'ru' });
}

fetch('/map/get-geo.php?type=regions')
    .then(response => response.json())
    .then(data => {
        renderRegions(data);
    })
    .catch(error => {
        console.error('Ошибка загрузки регионов:', error);
    });

function renderRegions(regionsData) {
    const $regions = $('#regions');
    let html = '<option value=""></option>';
    regionsData.forEach(region => {
        const code = regionCodes[region.name] ? `[${regionCodes[region.name]}] ` : '';
        html += `<option value="${region.id}" data-name="${region.name}">${code}${region.name}</option>`;
    });
    $regions.html(html).prop('disabled', false);
    if ($regions.data('select2')) {
        $regions.select2('destroy');
    }
    $regions.select2({ placeholder: 'Выберите регион', width: '100%', language: 'ru' });

    $regions.off('change.finder').on('change.finder', function () {
        const regionId = $(this).val();
        const regionName = $(this).find(':selected').data('name');
        if (!regionId) {
            return;
        }

        selectedRegion = regionName;
        selectedDistrict = '';
        selectedDistrictId = '';
        selectedSettlement = '';

        renderDistricts(regionId, regionName);
        getProjects(selectedRegion, selectedDistrict, selectedSettlement);
    });

    resetFinderSelect('#districts', 'Сначала выберите регион');
    resetFinderSelect('#settlements', 'Сначала выберите район');
    document.getElementById('projects').innerHTML = '';
}

function renderDistricts(regionId, regionName) {
    resetFinderSelect('#settlements', 'Сначала выберите район');
    document.getElementById('projects').innerHTML = '';

    fetch('/map/get-geo.php?type=districts&region_id=' + encodeURIComponent(regionId))
        .then(response => response.json())
        .then(districts => {
            const $districts = $('#districts');
            let html = '<option value=""></option>';
            districts.forEach(distr => {
                html += `<option value="${distr.id}" data-name="${distr.name}">${distr.name}</option>`;
            });
            $districts.html(html).prop('disabled', false);
            if ($districts.data('select2')) {
                $districts.select2('destroy');
            }
            $districts.select2({ placeholder: 'Выберите район', width: '100%', language: 'ru' });

            $districts.off('change.finder').on('change.finder', function () {
                const districtId = $(this).val();
                const districtName = $(this).find(':selected').data('name');
                if (!districtId) {
                    return;
                }

                selectedDistrict = districtName;
                selectedDistrictId = districtId;
                selectedSettlement = '';

                renderSettlements(districtId);
                getProjects(selectedRegion, selectedDistrict, selectedSettlement);
            });
        })
        .catch(error => {
            console.error('Ошибка загрузки районов:', error);
        });
}

function renderSettlements(districtId) {
    document.getElementById('projects').innerHTML = '';

    fetch('/map/get-geo.php?type=settlements&district_id=' + encodeURIComponent(districtId))
        .then(response => response.json())
        .then(settlements => {
            currentSettlementsData = settlements;

            const $settlements = $('#settlements');
            let html = '<option value=""></option>';
            settlements.forEach(settl => {
                html += `<option value="${settl.name}">${settl.name}</option>`;
            });
            $settlements.html(html).prop('disabled', false);
            if ($settlements.data('select2')) {
                $settlements.select2('destroy');
            }
            $settlements.select2({
                placeholder: 'Выберите или введите населённый пункт',
                width: '100%',
                language: 'ru',
                tags: true
            });

            $settlements.off('change.finder').on('change.finder', function () {
                const value = $(this).val();
                if (!value) {
                    return;
                }
                selectedSettlement = value;
                getProjects(selectedRegion, selectedDistrict, selectedSettlement);
            });

            $settlements.off('select2:select').on('select2:select', function (e) {
                const text = e.params.data.text;
                const exists = currentSettlementsData.some(s => s.name.toLowerCase() === text.toLowerCase());
                if (!exists) {
                    submitCustomSettlement(text, districtId);
                }
            });
        })
        .catch(error => {
            console.error('Ошибка загрузки населённых пунктов:', error);
        });
}

function submitCustomSettlement(name, districtId) {
    fetch('/map/add-settlement.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ district_id: districtId, name: name })
    })
        .then(response => response.json())
        .then(result => {
            if (!result.success) {
                console.error('Ошибка добавления населённого пункта:', result.error);
                return;
            }
            if (!result.active) {
                const $option = $('#settlements option').filter(function () {
                    return $(this).val() === name;
                });
                $option.text(name + ' (на модерации)');
                $('#settlements').trigger('change.select2');
            }
        })
        .catch(error => {
            console.error('Ошибка добавления населённого пункта:', error);
        });
}

function getProjects(regionName = '', districtName = '', settlement = '') {
    const projectsDiv = document.getElementById('projects');

    showLoader(projectsDiv);

    fetch('/map/get-project.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            region: regionName,
            district: districtName,
            settlement: settlement
        })
    })
        .then(r => r.text())
        .then(html => {
            hideLoader(projectsDiv);
            const shouldScroll = settlement !== '';
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

function showLoader(container) {
    container.innerHTML = `
        <div class="projects-loader">
            <div class="loader-spinner"></div>
        </div>
    `;
}

function hideLoader(container) {
    const loader = container.querySelector('.projects-loader');
    if (loader) {
        loader.remove();
    }
}

function renderProjects(html, shouldScroll = false) {
    const projectsDiv = document.getElementById('projects');
    if (!html || html.trim() === '') {
        projectsDiv.innerHTML = `<div class="no-projects">
                    <h1>Проекты не найдены.</h1>
                    <p class="no-projects__hint">Станьте первым — подайте заявку на проект для этого места.</p>
                    <button type="button" class="main-page__create-button no-projects__create-button">Создать проект</button>
                </div>`;
        const createButton = projectsDiv.querySelector('.no-projects__create-button');
        if (createButton) {
            createButton.addEventListener('click', function () {
                if (window.isUserAuthorized) {
                    redirect('/projects/create/');
                } else {
                    openAuth('/projects/create/');
                }
            });
        }
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

            if (shouldScroll && isMobile() && projectsDiv) {
                setTimeout(() => {
                    projectsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        }, 100);
    }
}

$(function () {
    resetFinderSelect('#regions', 'Загрузка регионов…');
    resetFinderSelect('#districts', 'Сначала выберите регион');
    resetFinderSelect('#settlements', 'Сначала выберите район');
});
