<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');?>
<? $APPLICATION->SetTitle("Жёлтый Грейдер - главная страница");?>
    <div class="main-page__content">
        <div class="main-page__cta">
            <div class="main-page__info">
                <h1>Вместе <br/> сделаем <br/> дороги лучше!</h1>
                <div class="main-page__actions-wrapper">
                    <? if ($USER->IsAuthorized()): ?>
                    <button onclick="redirect('/projects/create.php')" class="main-page__create-button">Создать проект</button>
                    <? else: ?>
                    <button onclick="openAuth();" class="main-page__create-button">Создать проект</button>
                    <? endif;?>
                    <button onclick="redirect('/about/')" class="main-page__more-button">Подробнее</button>
                </div>
            </div>

            <div class="main-page__orderCallback">
                <a href="#">
                    <button onclick="openDialog(document.getElementById('callBackDialog'))">
                        <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="2" width="68" height="68" rx="34" stroke="#FFD800" stroke-width="4"/>
                            <path d="M40.776 23.6452C42.6464 24.0094 44.3654 24.9225 45.7129 26.2674C47.0605 27.6123 47.9752 29.328 48.3401 31.1949M40.776 16C44.662 16.4309 48.2857 18.1678 51.0522 20.9254C53.8186 23.6831 55.5634 27.2977 56 31.1758M54.085 46.428V52.1619C54.0872 52.6942 53.978 53.2211 53.7643 53.7088C53.5506 54.1965 53.2373 54.6343 52.8443 54.9942C52.4513 55.354 51.9873 55.628 51.4821 55.7985C50.9769 55.9691 50.4416 56.0324 49.9104 55.9845C44.0177 55.3454 38.3574 53.3357 33.3843 50.1168C28.7574 47.1823 24.8346 43.267 21.8945 38.649C18.6582 33.6628 16.6441 27.9858 16.0155 22.0779C15.9677 21.5494 16.0306 21.0167 16.2003 20.5138C16.3701 20.0109 16.6428 19.5487 17.0013 19.1568C17.3598 18.7648 17.7961 18.4516 18.2825 18.2372C18.7688 18.0228 19.2946 17.9118 19.8263 17.9113H25.5712C26.5006 17.9022 27.4015 18.2306 28.1062 18.8355C28.8108 19.4403 29.2711 20.2803 29.4011 21.1987C29.6436 23.0337 30.0933 24.8354 30.7416 26.5695C30.9993 27.2536 31.055 27.9971 30.9023 28.7119C30.7496 29.4266 30.3947 30.0827 29.8799 30.6024L27.4479 33.0297C30.1739 37.8148 34.1435 41.7767 38.9377 44.4975L41.3697 42.0702C41.8903 41.5563 42.5477 41.2022 43.2638 41.0497C43.9799 40.8973 44.7248 40.9529 45.4102 41.2101C47.1476 41.8572 48.9528 42.306 50.7913 42.548C51.7215 42.679 52.5711 43.1466 53.1784 43.862C53.7857 44.5774 54.1083 45.4906 54.085 46.428Z" stroke="#FFD800" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </a>
            </div>
        </div>
    </div>
    <div class="main-page__container">
        <? $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "projects-list",
        Array(
        "ACTIVE_DATE_FORMAT" => "j F Y",
        "AJAX_OPTION_JUMP" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "A",
        "DISPLAY_DATE" => "Y",
        "IBLOCK_ID" => "1",
        "PARENT_SECTION_CODE" => "ongoing",
        "STATUS" => 'ongoing',
        "IBLOCK_TYPE" => "news",
        "FIELD_CODE" => array("NAME"),
        "PROPERTY_CODE" => array("PR_PHOTO","PR_SQUARE","PR_DEADLINES", "PR_COST"),
        "INCLUDE_SUBSECTIONS" => "Y",
        "NEWS_COUNT" => "3",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TEMPLATE" => "pagenav",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "SET_STATUS_404" => "N",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        )
        );?>
    </div>
    <div>
        <section class="main-page__benefits">
            <div class="main-page__benefits__content">
                <h1>Какие выгоды</h1>
                <h2>вы получаете, выбрав нас?</h2>
                <div class="main-page__benefits-list">
                    <div class="main-page__benefits-item">
                        <h3>Независимость</h3>
                        <p>Возможность самостоятельно принимать решения без ограничений и вмешательства государства.</p>
                    </div>
                    <div class="main-page__benefits-item">
                        <h3>Быстрая реакция</h3>
                        <p>Устраняем проблему и повышаем удобства и безопасность передвижения.</p>
                    </div>
                    <div class="main-page__benefits-item">
                        <h3>Гибкое планирование</h3>
                        <p>Возможность внести вклад в развитие своего региона, включая более сложные и дорогостоящие проекты.</p>
                    </div>
                    <div class="main-page__benefits-item">
                        <h3>Прозрачная отчётность</h3>
                        <p>Вся информация о собранной сумме и средствах, потраченных на ремонт дорог в доступна на сайте.</p>
                    </div>
                    <div class="main-page__benefits-item">
                        <h3>Просто, быстро и удобно</h3>
                        <p>Создать проект можно самостоятельно в мобильной версии сайта или позвонив менеджеру.</p>
                    </div>
                    <div class="main-page__benefits-item">
                        <h3>Вместе мы сила</h3>
                        <p>Через совместную реализацию проектов, помогаем найти единомышленников.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="main-page__container">
        <? $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "projects-list",
            Array(
                "ACTIVE_DATE_FORMAT" => "j F Y",
                "AJAX_OPTION_JUMP" => "N",
                "CACHE_GROUPS" => "Y",
                "CACHE_TIME" => "36000",
                "CACHE_TYPE" => "A",
                "DISPLAY_DATE" => "Y",
                "IBLOCK_ID" => "1",
                "PARENT_SECTION_CODE" => "result",
                "STATUS" => 'completed',
                "IBLOCK_TYPE" => "news",
                "FIELD_CODE" => array("NAME"),
                "PROPERTY_CODE" => array("PR_PHOTO","PR_SQUARE","PR_DEADLINES", "PR_COST"),
                "INCLUDE_SUBSECTIONS" => "Y",
                "NEWS_COUNT" => "3",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TEMPLATE" => "pagenav",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "SET_STATUS_404" => "N",
                "SORT_BY1" => "ACTIVE_FROM",
                "SORT_ORDER1" => "DESC",
            )
        );?>
    </div>
    <dialog id="callBackDialog" class="dialog">
        <div class="close-dialog">
            <a href="#">
                <button  onclick="closeDialog(document.getElementById('callBackDialog'))">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
                    </svg>
                </button>
            </a>
        </div>
        <div class="callback">
            <div class="modal-content">
                <h1>Добрый день!</h1>
                <h1>Вам нужна помощь в создании проекта? Укажите номер телефона, по которому мы можем перезвонить.</h1>
            </div>
            <form method="dialog" class="content" id="callbackForm">
                <div class="callback__form">
                    <div class="modal-field">
                        <label for="phone"><span class="required-sign">*</span>Телефон</label>
                        <input required name="phone"  id="phone"  placeholder="+ 7 (ХХХ) ХХХ ХХ ХХ"/>
                    </div>
                    <p class="required-field-annotation"><span class="required-sign">*</span> поля обязательные к заполнению</p>
                    <?php
                    $consentIdPrefix = 'callback';
                    include $_SERVER['DOCUMENT_ROOT'] . '/include/consent-checkboxes.php';
                    ?>
                    <div class="modal__choose-block">
                        <a href="#">
                            <button type="submit" id="getCode" class="modal-submit-button">Позвоните мне</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </dialog>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>


