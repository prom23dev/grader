<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true); ?>
<? if (preg_match('#^/projects(?:/|$)#', $APPLICATION->GetCurPage(false))): ?>
    <form class="search__block search__block--with-geo" action="" method="get">
        <label class="search__input-block">
            <input type="search" name="q" value="<?= htmlspecialcharsbx($_REQUEST['q'] ?? '') ?>" class="search__input-field"
                   placeholder="№ XX-XXX"/>
            <button type="submit" aria-label="Поиск">
                <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                          stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.9999 21.0004L16.6499 16.6504" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>
        </label>
        <label class="search__geo-select-block">
            <select name="region_id" id="search-region-select" class="search__geo-select" data-selected="<?= (int)($_REQUEST['region_id'] ?? 0) ?>">
                <option value="">Любой регион</option>
            </select>
        </label>
        <label class="search__geo-select-block">
            <select name="district_id" id="search-district-select" class="search__geo-select" data-selected="<?= (int)($_REQUEST['district_id'] ?? 0) ?>" disabled>
                <option value="">Любой район</option>
            </select>
        </label>
    </form>
    <script>
        (function () {
            var regionSelect = document.getElementById('search-region-select');
            var districtSelect = document.getElementById('search-district-select');
            if (!regionSelect || !districtSelect) {
                return;
            }

            function fillDistricts(regionId, selectedDistrictId) {
                districtSelect.innerHTML = '<option value="">Любой район</option>';
                if (!regionId) {
                    districtSelect.disabled = true;
                    return;
                }
                districtSelect.disabled = false;
                fetch('/map/get-geo.php?type=districts&region_id=' + encodeURIComponent(regionId))
                    .then(function (r) { return r.json(); })
                    .then(function (districts) {
                        districts.forEach(function (district) {
                            var opt = document.createElement('option');
                            opt.value = district.id;
                            opt.textContent = district.name;
                            if (String(district.id) === String(selectedDistrictId)) {
                                opt.selected = true;
                            }
                            districtSelect.appendChild(opt);
                        });
                    });
            }

            fetch('/map/get-geo.php?type=regions')
                .then(function (r) { return r.json(); })
                .then(function (regions) {
                    var selectedRegionId = regionSelect.dataset.selected;
                    regions.forEach(function (region) {
                        var opt = document.createElement('option');
                        opt.value = region.id;
                        opt.textContent = region.name;
                        if (String(region.id) === String(selectedRegionId)) {
                            opt.selected = true;
                        }
                        regionSelect.appendChild(opt);
                    });
                    if (selectedRegionId && selectedRegionId !== '0') {
                        fillDistricts(selectedRegionId, districtSelect.dataset.selected);
                    }
                });

            regionSelect.addEventListener('change', function () {
                fillDistricts(regionSelect.value, '');
            });
        })();
    </script>
<? endif; ?>
<? if (preg_match('/\/auth\/.+\//', $APPLICATION->GetCurPage())): ?>
    <form class="my-projects__search__block" method="get">
        <label class="search__input-block">
            <input name="q" value="<?= htmlspecialcharsbx($_REQUEST['q'] ?? '') ?>" class="my-projects__search__input-field"
                   placeholder="№ XX-XXX" type="search"/>
            <button type="submit" aria-label="Поиск">
                <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                          stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.9999 21.0004L16.6499 16.6504" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>
        </label>
    </form>
<? endif; ?>
