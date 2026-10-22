<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? $currentCity = resolveCurrentCity(); ?>

<? if (!empty($arResult)): ?>
    <nav>
        <ul class="header__navigation-list">
            <?
            foreach ($arResult as $arItem):
                if ($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                    continue;
                ?>
                <? if ($arItem["SELECTED"]):?>
                <li><a href="<?= $arItem["LINK"] ?>" class="selected"><?= $arItem["TEXT"] ?></a></li>
            <? else:?>
                <li><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a></li>
            <? endif ?>

            <? endforeach ?>
            <li class="header__actions">
                <a href="/projects/support.php" class="header__support-btn">Поддержать проект</a>
                <a href="tel:+79094568723" class="header__icon-btn" aria-label="Позвонить">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27097 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36163C2.30512 3.0985 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.04981 2.28271 3.30379 2.17052C3.55778 2.05833 3.83234 2.00026 4.10999 2H7.10999C7.59522 1.99522 8.06574 2.16708 8.43376 2.48353C8.80178 2.79999 9.04213 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.8939 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5865 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <? if ($USER->IsAuthorized()): ?>
                <a href="/personal/profile/" class="header__icon-btn" aria-label="Личный кабинет">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <? else: ?>
                    <a onclick="openAuth()" class="header__icon-btn" aria-label="Личный кабинет">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                                  stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"
                                  stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <? endif;?>
            </li>
        </ul>

        <div class="header__city-popup" id="headerCityPopup" data-selected-city-id="<?= (int)$currentCity['id'] ?>">
            <button type="button" class="header__city-popup-close" onclick="toggleCityPopup()" aria-label="Закрыть">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18" stroke="#474A51" stroke-width="2" stroke-linecap="round"/>
                    <path d="M6 6L18 18" stroke="#474A51" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            <p class="header__city-popup-label">Выберите населённый пункт</p>
            <input type="text" class="header__city-popup-search" placeholder="Поиск города..." autocomplete="off">
            <div class="header__city-popup-list" id="headerCityPopupList">
                <p class="header__city-popup-hint">Введите название населённого пункта</p>
            </div>
        </div>

        <? if (!empty($currentCity['needsConfirm'])): ?>
            <div class="header__city-confirm" id="headerCityConfirm" data-city-id="<?= (int)$currentCity['id'] ?>" data-city-name="<?= htmlspecialcharsbx($currentCity['name']) ?>">
                <p class="header__city-confirm-text">Ваш город — «<?= htmlspecialcharsbx($currentCity['name']) ?>»?</p>
                <div class="header__city-confirm-actions">
                    <button type="button" class="header__city-confirm-btn header__city-confirm-btn--yes" id="headerCityConfirmYes">Да</button>
                    <button type="button" class="header__city-confirm-btn header__city-confirm-btn--no" id="headerCityConfirmNo">Нет</button>
                </div>
            </div>
        <? endif; ?>

        <ul class="header__mobile--navigation-list">
            <li>
                <a href="/projects/support.php" class="header__support-btn header__support-btn--mobile">Поддержать</a>
            </li>
            <li>
                <a href="tel:+79094568723" aria-label="Позвонить">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27097 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36163C2.30512 3.0985 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.04981 2.28271 3.30379 2.17052C3.55778 2.05833 3.83234 2.00026 4.10999 2H7.10999C7.59522 1.99522 8.06574 2.16708 8.43376 2.48353C8.80178 2.79999 9.04213 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.8939 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5865 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
            <li>
                <? if ($USER->IsAuthorized()): ?>
                <a href="/personal/profile/">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.3332 14V12.6667C13.3332 11.9594 13.0522 11.2811 12.5521 10.781C12.052 10.281 11.3737 10 10.6665 10H5.33317C4.62593 10 3.94765 10.281 3.44755 10.781C2.94746 11.2811 2.6665 11.9594 2.6665 12.6667V14"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.00016 7.33333C9.47292 7.33333 10.6668 6.13943 10.6668 4.66667C10.6668 3.19391 9.47292 2 8.00016 2C6.5274 2 5.3335 3.19391 5.3335 4.66667C5.3335 6.13943 6.5274 7.33333 8.00016 7.33333Z"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <? else:?>
                <a onclick="openAuth()">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.3332 14V12.6667C13.3332 11.9594 13.0522 11.2811 12.5521 10.781C12.052 10.281 11.3737 10 10.6665 10H5.33317C4.62593 10 3.94765 10.281 3.44755 10.781C2.94746 11.2811 2.6665 11.9594 2.6665 12.6667V14"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.00016 7.33333C9.47292 7.33333 10.6668 6.13943 10.6668 4.66667C10.6668 3.19391 9.47292 2 8.00016 2C6.5274 2 5.3335 3.19391 5.3335 4.66667C5.3335 6.13943 6.5274 7.33333 8.00016 7.33333Z"
                              stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <? endif; ?>
            </li>
            <li>
                <a onclick="toggleMenu()" href="#">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 8H14" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                        <path d="M2 4H14" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                        <path d="M2 12H14" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
    <!--        сделать мобильное меню-->
    <div class="mobile-menu" id="mobile-menu">
        <button onclick="toggleMenu()" class="close-menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.1213 11.849L20.4852 5.4851L18.3639 3.36377L11.9999 9.72773L5.63598 3.36377L3.51466 5.48509L9.87862 11.849L3.51465 18.213L5.63597 20.3343L11.9999 13.9704L18.3639 20.3343L20.4852 18.213L14.1213 11.849Z" fill="#474A51"/>
            </svg>
        </button>

        <div class="mobile-menu__content">
            <a href="/projects/support.php" class="header__support-btn mobile-menu__support-btn">Поддержать проект</a>
            <nav class="mobile-menu__list">
                <?
                foreach ($arResult as $arItem):
                    if ($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                        continue;
                    ?>
                    <a href="<?= $arItem["LINK"] ?>" class="mobile-menu__list_item"><?= $arItem["TEXT"] ?></a>
                <? endforeach ?>
        </nav>
        <a href="tel:+79094568723" class="mobile-menu__phone" aria-label="Позвонить по номеру +7 (909) 456-87-23">
            +7 (909) 456-87-23
        </a>
        </div>
    </div>
<? endif ?>
