<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJS("https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js");
Asset::getInstance()->addJS("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js");
Asset::getInstance()->addJS("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js");
Asset::getInstance()->addJS("https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <? $APPLICATION->ShowHead(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $pagePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $pageTitles = [
        '/' => 'Жёлтый Грейдер — главная страница',
        '/about/' => 'О портале — Жёлтый Грейдер',
        '/news/' => 'Новости — Жёлтый Грейдер',
        '/reviews/' => 'Отзывы — Жёлтый Грейдер',
        '/projects/result/' => 'Результаты проектов — Жёлтый Грейдер',
        '/projects/ongoing/' => 'Действующие проекты — Жёлтый Грейдер',
        '/projects/completed/' => 'Завершённые проекты — Жёлтый Грейдер',
        '/projects/support.php' => 'Поддержать проект — Жёлтый Грейдер',
        '/projects/create/' => 'Создать проект — Жёлтый Грейдер',
        '/projects/create.php' => 'Создать проект — Жёлтый Грейдер',
    ];
    ?>
    <title><?= htmlspecialcharsbx($pageTitles[$pagePath] ?? $APPLICATION->GetTitle()) ?></title>

    <link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH . '/assets/img/favicon/favicon-16x16.png' ?>"
          sizes="16x16"/>
    <link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH . '/assets/img/favicon/favicon-32x32.png' ?>"
          sizes="32x32"/>
    <link rel="icon" type="image/png" href="<?= SITE_TEMPLATE_PATH . '/assets/img/favicon/favicon-96x96.png' ?>"
          sizes="96x96"/>

    <!--    <link rel="icon" type="image/svg+xml" href="-->
    <?php //=SITE_TEMPLATE_PATH.'/assets/img/favicon/favicon.svg'?><!--" />-->
    <!--    <link rel="shortcut icon" href="-->
    <?php //=SITE_TEMPLATE_PATH.'/assets/img/favicon/favicon.ico'?><!--" />-->
    <!--    <link rel="apple-touch-icon" sizes="180x180" href="-->
    <?php //=SITE_TEMPLATE_PATH.'/assets/img/favicon/apple-touch-icon.png'?><!--" />-->
    <!--    <link rel="manifest" href="-->
    <?php //=SITE_TEMPLATE_PATH.'/assets/img/favicon/site.webmanifest'?><!--" />-->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
</head>
<body>
<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<? //$APPLICATION->IncludeComponent(
//    "bitrix:sale.basket.basket.line",
//    "",
//    Array(
//        "HIDE_ON_BASKET_PAGES" => "Y",
//        "PATH_TO_AUTHORIZE" => "/personal/index.php",
//        "PATH_TO_BASKET" => SITE_DIR."personal/cart/",
//        "PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
//        "PATH_TO_PERSONAL" => "/personal/personal.php",
//        "PATH_TO_PROFILE" => "/personal/personal.php",
//        "PATH_TO_REGISTER" => "/personal/registration.php",
//        "POSITION_FIXED" => "N",
//        "SHOW_AUTHOR" => "N",
//        "SHOW_EMPTY_VALUES" => "Y",
//        "SHOW_NUM_PRODUCTS" => "Y",
//        "SHOW_PERSONAL_LINK" => "Y",
//        "SHOW_PRODUCTS" => "N",
//        "SHOW_REGISTRATION" => "Y",
//        "SHOW_TOTAL_PRICE" => "Y"
//    )
//);?>
<?
$currentCity = resolveCurrentCity();
?>
<header>
    <div class="header__block">
        <div class="header__left">
            <a href="/" class="header__site-title header__logo">
                <img class="header__logo-full" src="/front/img/logo-dark.svg" alt="logo"/>
                <img class="header__logo-icon" src="/front/img/logo-icon.svg" alt="logo"/>
            </a>
            <button type="button" class="header__city-pill header__city-pill--mobile" id="headerCityBtnMobile" onclick="toggleCityPopup()" aria-expanded="false" aria-label="Выбрать город">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 21C12 21 19 15.5 19 10C19 6.13401 15.866 3 12 3C8.13401 3 5 6.13401 5 10C5 15.5 12 21 12 21Z" stroke="#173A7C" stroke-width="2" stroke-linejoin="round"/>
                    <circle cx="12" cy="10" r="2.5" stroke="#173A7C" stroke-width="2"/>
                </svg>
                <span class="header__city-name" id="headerCityNameMobile"><?= htmlspecialcharsbx(preg_replace('/\s*\([^)]*\)\s*$/', '', (string)$currentCity['name'])) ?></span>
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 9L12 15L18 9" stroke="#173A7C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>

        <? $APPLICATION->IncludeComponent(
            "bitrix:menu",
            "header-menu",
            array(
                "ALLOW_MULTI_SELECT" => "N",
                "DELAY" => "N",
                "MAX_LEVEL" => "1",
                "MENU_CACHE_GET_VARS" => array(""),
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_TYPE" => "N",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "ROOT_MENU_TYPE" => "top",
                "USE_EXT" => "N"
            )
        ); ?>

    </div>

    <?
    $headerAnnounceItems = getActiveHeaderAnnouncements();
    ?>
    <div class="header__announce<?= $headerAnnounceItems ? '' : ' header__announce--hidden' ?>" id="headerAnnounce">
        <button type="button" class="header__announce-arrow header__announce-arrow--prev" id="headerAnnouncePrev" aria-label="Предыдущее объявление">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 12L6 8L10 4" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <div class="header__announce-track" id="headerAnnounceTrack">
            <? foreach ($headerAnnounceItems as $i => $announceItem): ?>
                <? if ($announceItem['link']): ?>
                    <a href="<?= htmlspecialcharsbx($announceItem['link']) ?>" class="header__announce-item<?= $i === 0 ? ' header__announce-item--active' : '' ?>"><?= htmlspecialcharsbx($announceItem['text']) ?></a>
                <? else: ?>
                    <span class="header__announce-item<?= $i === 0 ? ' header__announce-item--active' : '' ?>"><?= htmlspecialcharsbx($announceItem['text']) ?></span>
                <? endif; ?>
            <? endforeach; ?>
        </div>
        <button type="button" class="header__announce-arrow header__announce-arrow--next" id="headerAnnounceNext" aria-label="Следующее объявление">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 4L10 8L6 12" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</header>
<script>
    (function () {
        var track = document.getElementById('headerAnnounceTrack');
        var block = document.getElementById('headerAnnounce');
        var prevBtn = document.getElementById('headerAnnouncePrev');
        var nextBtn = document.getElementById('headerAnnounceNext');
        var index = 0;
        var timer = null;

        function escapeHtml(str) {
            var div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function renderItems(data) {
            track.innerHTML = data.map(function (item, i) {
                var tag = item.link ? 'a' : 'span';
                var attrs = 'class="header__announce-item' + (i === 0 ? ' header__announce-item--active' : '') + '"';
                if (item.link) attrs += ' href="' + escapeHtml(item.link) + '"';
                return '<' + tag + ' ' + attrs + '>' + escapeHtml(item.text) + '</' + tag + '>';
            }).join('');
            block.style.display = data.length ? '' : 'none';
            index = 0;
            init();
        }

        function stopAutoplay() {
            clearInterval(timer);
            timer = null;
        }

        // Элементы карусели абсолютно спозиционированы (нужно для кроссфейда),
        // поэтому не участвуют в высоте track — высоту ставим вручную, иначе
        // многострочный текст на мобильных перекрывает блок под шапкой.
        function updateHeight() {
            var active = track.querySelector('.header__announce-item--active');
            if (active) track.style.height = active.scrollHeight + 'px';
        }

        function init() {
            stopAutoplay();
            var items = track.querySelectorAll('.header__announce-item');

            function show(newIndex) {
                items[index].classList.remove('header__announce-item--active');
                index = (newIndex + items.length) % items.length;
                items[index].classList.add('header__announce-item--active');
                updateHeight();
            }

            updateHeight();

            if (items.length > 1) {
                if (prevBtn) prevBtn.style.display = '';
                if (nextBtn) nextBtn.style.display = '';
                timer = setInterval(function () { show(index + 1); }, 4000);
                if (nextBtn) nextBtn.onclick = function () { show(index + 1); stopAutoplay(); timer = setInterval(function () { show(index + 1); }, 4000); };
                if (prevBtn) prevBtn.onclick = function () { show(index - 1); stopAutoplay(); timer = setInterval(function () { show(index + 1); }, 4000); };
            } else {
                if (nextBtn) nextBtn.style.display = 'none';
                if (prevBtn) prevBtn.style.display = 'none';
            }
        }

        if (track) init();

        var resizeTimer = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(updateHeight, 150);
        });

        // Перезапрос объявлений без перезагрузки страницы при смене города
        // в попапе шапки (см. header-menu/script.js: selectCity() кидает
        // это событие после обновления cookie SELECTED_CITY_ID).
        document.addEventListener('grader:cityChanged', function () {
            if (!track) return;
            fetch('/local/templates/grader/assets/ajax/getHeaderAnnouncements.php', {credentials: 'same-origin'})
                .then(function (r) { return r.json(); })
                .then(renderItems)
                .catch(function () {});
        });
    })();
</script>
<? if ($APPLICATION->GetCurPage(false) !== '/'): ?>
    <div>
        <ol id="breadcrumbs">
            <? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "breadcrumbs",
                array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0"
                )
            ); ?>
        </ol>
    </div>
<? endif;?>

