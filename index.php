<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
?>
<link rel="stylesheet" href="/local/templates/grader/components/bitrix/news.list/projects-list/style.css">
<link rel="stylesheet" href="/map/assets/css/style.css">
<link rel="stylesheet" href="/local/templates/grader/components/bitrix/search.form/grader-search/style.css">

<div class="main-page__hero">
    <h1 class="main-page__hero-title">Соберите средства на ремонт дороги в вашем районе</h1>
    <p class="main-page__hero-subtitle">Сообщите о проблемной дороге, соберите средства с единомышленниками — мы организуем ремонт.</p>
    <form class="main-page__hero-search" action="/projects/" method="get">
        <label class="search__input-block">
            <input type="search" name="q" value="<?= htmlspecialcharsbx($_REQUEST['q'] ?? '') ?>" class="search__input-field" placeholder="Поиск проекта по номеру или адресу"/>
            <button type="submit" aria-label="Поиск">
                <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.9999 21.0004L16.6499 16.6504" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </label>
    </form>
    <div class="main-page__hero-actions">
        <? if ($USER->IsAuthorized()): ?>
            <button onclick="redirect('/projects/create/')" class="main-page__create-button main-page__hero-button">
                <svg class="main-page__hero-button-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M8 2.5V13.5M2.5 8H13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Создать проект
            </button>
        <? else: ?>
            <button onclick="openAuth('/projects/create/');" class="main-page__create-button main-page__hero-button">
                <svg class="main-page__hero-button-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M8 2.5V13.5M2.5 8H13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Создать проект
            </button>
        <? endif; ?>
        <a href="/projects/support.php" class="main-page__more-button main-page__hero-button">
            <svg class="main-page__hero-button-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M8 13.5C8 13.5 1.5 9.636 1.5 5.5C1.5 3.567 3.067 2 5 2C6.306 2 7.44 2.716 8 3.776C8.56 2.716 9.694 2 11 2C12.933 2 14.5 3.567 14.5 5.5C14.5 9.636 8 13.5 8 13.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
            Поддержать проект
        </a>
    </div>
</div>

<div class="main-page__finder">
    <p class="map-selector-instruction">Где у вас проблема с дорогой?</p>
    <p class="map-selector-instruction-sub">Выберите три шага — и увидите действующие проекты рядом с вами</p>
    <div class="map-selector-panel">
        <div class="map-container-wrapper">
            <label class="map-container-title" for="regions"><span class="map-step-number">1</span> Регион</label>
            <select id="regions" class="map-select"></select>
        </div>
        <div class="map-container-wrapper">
            <label class="map-container-title" for="districts"><span class="map-step-number">2</span> Район</label>
            <select id="districts" class="map-select" disabled></select>
        </div>
        <div class="map-container-wrapper">
            <label class="map-container-title" for="settlements"><span class="map-step-number">3</span> Населённый пункт</label>
            <select id="settlements" class="map-select" disabled></select>
        </div>
    </div>
</div>

<div class="map-wrapper">
    <div id="projects" class="map-container"></div>
</div>
<script>
    window.isUserAuthorized = <?= $USER->IsAuthorized() ? 'true' : 'false' ?>;
</script>
<script src="/map/assets/js/homepage-finder.js"></script>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
