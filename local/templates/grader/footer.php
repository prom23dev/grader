<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_DIR . "front/css/footer-extra.css");
?>
<?
if (!$USER->IsAuthorized()) {

    if ($_REQUEST['REGISTER']['PERSONAL_BIRTHDAY']) {
        $_REQUEST['REGISTER']['PERSONAL_BIRTHDAY'] = FormatDate('j.m.Y', MakeTimeStamp($_REQUEST['REGISTER']['PERSONAL_BIRTHDAY']));
        var_dump($_REQUEST['REGISTER']['PERSONAL_BIRTHDAY']);
    }

    $APPLICATION->IncludeComponent(
	"bitrix:main.register",
	"registration",
	Array(
		"AUTH" => "Y",
		"REQUIRED_FIELDS" => array("EMAIL","NAME","LAST_NAME","PERSONAL_PHONE"),
		"SET_TITLE" => "Y",
		"SHOW_FIELDS" => array("EMAIL","NAME","SECOND_NAME","LAST_NAME","PERSONAL_GENDER","PERSONAL_BIRTHDAY","PERSONAL_PHONE", "PASSWORD", "CONFIRM_PASSWORD"),
		"SUCCESS_PAGE" => "/",
		"USER_PROPERTY" => array(),
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y"
	)
);
    $APPLICATION->IncludeComponent("bitrix:system.auth.form", "authorization", Array(
        "FORGOT_PASSWORD_URL" => "/personal/forget.php",	// Страница забытого пароля
        "PROFILE_URL" => "/personal/profile/index.php",	// Страница профиля
        "REGISTER_URL" => "",	// Страница регистрации
        "SHOW_ERRORS" => "N",	// Показывать ошибки
    ),
        false
    );
    $APPLICATION->IncludeFile(SITE_DIR."auth/forget.php",Array(), Array("MODE"=>"PHP"));
    if($_GET['logout_butt']==='Выйти'){
        header("Location:/");
    }

    $APPLICATION->IncludeComponent(
        "bitrix:system.auth.changepasswd",
        "changepasswd",
        array(
            "SHOW_ERRORS" => "Y"
        )
    );

}
?>
<script src="<?=SITE_TEMPLATE_PATH.'/assets/js/authorize.js'?>"></script>
<footer id="footer">
    <div class="footer__site-title-copyright-block">
        <a href="/" class="footer__site-title footer__logo">
            <img src="/front/img/logo-dark.svg" alt="logo"/>
        </a>
        <p class="footer__tablet-copyright-text">© «Желтый грейдер», 2024</p>
    </div>
    <div class="footer__information-links">
        <div class="footer-share">
            <button class="footer-share__trigger" type="button" aria-expanded="false" aria-controls="footer-share-menu">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="18" cy="5" r="2.5" stroke="currentColor" stroke-width="1.8"></circle><circle cx="6" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"></circle><circle cx="18" cy="19" r="2.5" stroke="currentColor" stroke-width="1.8"></circle><path d="m8.3 10.8 7.4-4.4M8.3 13.2l7.4 4.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path></svg>
                <span>Поделиться</span>
            </button>
            <div class="footer-share__menu" id="footer-share-menu" role="menu">
                <button type="button" class="footer-share__item" data-share-service="max"><span class="footer-share__icon footer-share__icon--max" aria-label="max"><img src="https://upload.wikimedia.org/wikipedia/commons/1/12/%D0%9B%D0%BE%D0%B3%D0%BE%D1%82%D0%B8%D0%BF_MAX.svg" alt="MAX"></span>MAX</button>
                <button class="footer-share__item" type="button" data-share-service="vk"><span class="footer-share__icon footer-share__icon--vk" aria-label="vk"><img src="https://upload.wikimedia.org/wikipedia/commons/f/f3/VK_Compact_Logo_%282021-present%29.svg" alt="VK"></span>ВКонтакте</button>
                <button class="footer-share__item" type="button" data-share-service="telegram"><span class="footer-share__icon footer-share__icon--tg" aria-label="telegram"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="m21.5 3.5-3 16.1c-.2 1.1-.8 1.4-1.6.9l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.8.4l.3-4.6 8.4-7.6c.4-.4-.1-.6-.6-.2L7.1 13.9l-4.4-1.4c-1-.3-1-1 .2-1.5L20.1 4c.9-.3 1.7.2 1.4-.5z"></path></svg></span>Telegram</button>
                <button class="footer-share__item" type="button" data-share-service="whatsapp"><span class="footer-share__icon footer-share__icon--wa" aria-label="whatsapp"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3a8.9 8.9 0 0 0-7.7 13.4L3 21l4.8-1.3A9 9 0 1 0 12 3zm0 16a7 7 0 0 1-3.6-1l-.3-.2-2.8.8.8-2.7-.2-.3A7 7 0 1 1 12 19zm3.8-5.2c-.2-.1-1.2-.6-1.4-.6-.2-.1-.3-.1-.5.1l-.6.8c-.1.2-.3.2-.5.1a5.7 5.7 0 0 1-2.8-2.5c-.2-.3 0-.4.1-.6l.4-.5c.1-.1.1-.3 0-.4l-.6-1.4c-.2-.4-.4-.3-.5-.3h-.4c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3 1 2.5c.1.2 1.6 2.5 3.9 3.4 1.5.6 1.8.5 2.1.5.3 0 1.2-.5 1.4-.9.2-.4.2-.8.1-.9z"></path></svg></span>WhatsApp</button>
                <button class="footer-share__item" type="button" data-share-service="ok"><span class="footer-share__icon footer-share__icon--ok" aria-label="ok"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Logo%20Odnoklassniki%202023.svg" alt="Одноклассники"></span>Одноклассники</button>
                <button class="footer-share__item" type="button" data-share-service="copy"><span class="footer-share__icon footer-share__icon--copy">↗</span>Скопировать ссылку</button>
            </div>
        </div>
        <div class="footer__mobile-social-links">
            <a href="https://t.me/share/url" data-footer-share="telegram" target="_blank" rel="noopener" aria-label="Поделиться страницей в Telegram">
                <svg width="32" height="32" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="48" height="48" rx="24" fill="white"/>
                    <path d="M28.3622 34.1976L20.5157 27.9675C22.6067 26.0216 29.958 19.1759 30.3111 18.8349L30.3112 18.8348C30.488 18.664 30.6335 18.4736 30.7072 18.2691C30.7804 18.0662 30.8076 17.7475 30.5627 17.5054C30.3443 17.2895 30.0534 17.2858 29.866 17.3164C29.6594 17.3501 29.4451 17.4424 29.2367 17.5734L29.2366 17.5733L29.228 17.579L16.5596 25.9101L11.834 24.2636L11.8221 24.2587C11.8085 24.2529 11.7867 24.2433 11.7593 24.2298C11.7034 24.2022 11.6298 24.1611 11.5567 24.1074C11.3973 23.9905 11.3141 23.8705 11.3007 23.7592L11.3007 23.7585C11.2981 23.7372 11.3061 23.6503 11.5062 23.5084C11.5898 23.4492 11.6793 23.4015 11.7506 23.3681C11.7854 23.3517 11.814 23.3397 11.8326 23.3322C11.8419 23.3284 11.8487 23.3259 11.8523 23.3245L11.8546 23.3236L11.8552 23.3234L11.8555 23.3233L11.8645 23.3201L11.8751 23.3158L32.124 15.0899L32.1335 15.086L32.1418 15.0822L32.142 15.0821L32.1424 15.082L32.1426 15.0819L32.1428 15.0817L32.143 15.0817L32.1519 15.0778C32.1607 15.074 32.1748 15.0681 32.1935 15.0606C32.231 15.0455 32.286 15.0246 32.3523 15.0028C32.4893 14.9576 32.6549 14.9151 32.807 14.9031C32.9709 14.8903 33.0424 14.9188 33.0604 14.9309L33.0608 14.9312C33.0633 14.9328 33.0674 14.9356 33.074 14.9499C33.0816 14.9664 33.0951 15.0057 33.099 15.0805L29.5134 33.7993C29.503 33.8204 29.4874 33.8497 29.4666 33.8837C29.4168 33.965 29.3441 34.0612 29.2489 34.1401C29.1566 34.2166 29.0472 34.2738 28.9127 34.2929C28.7879 34.3106 28.6089 34.3 28.3622 34.1976ZM29.5289 33.7659C29.529 33.7656 29.5287 33.7663 29.528 33.7681L29.5283 33.7673L29.5289 33.7659Z"
                          fill="#474A51" stroke="#474A51"/>
                </svg>

            </a>
            <a href="https://wa.me/" data-footer-share="whatsapp" target="_blank" rel="noopener" aria-label="Поделиться страницей в WhatsApp">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="16" fill="white"/>
                    <path d="M9.6533 19.8142L9.70752 19.6143L9.60448 19.4346C8.9437 18.2822 8.55854 16.9092 8.55854 15.5416C8.55854 11.2111 12.0794 7.69995 16.4293 7.69995C18.5205 7.69995 20.5011 8.52189 21.9885 10.0041C23.4689 11.4794 24.2431 13.4511 24.3 15.4901C24.2963 19.8175 20.7769 23.325 16.4293 23.325C15.1092 23.325 13.8418 22.9964 12.6802 22.39L12.5089 22.3006L12.3219 22.3496L8.70866 23.2972L9.6533 19.8142ZM17.5905 19.9144L17.6219 19.9269L17.6547 19.9351C18.1914 20.0688 18.7266 20.0703 19.1401 20.0024C19.4449 19.9574 19.8199 19.7856 20.1278 19.5837C20.4274 19.3873 20.7904 19.0804 20.9363 18.6927C21.0407 18.4153 21.0947 18.1507 21.113 17.9223C21.1304 17.7053 21.1185 17.4844 21.0595 17.308L21.022 17.1959L20.9383 17.1124C20.8569 17.0313 20.761 16.9745 20.6885 16.9344C20.6161 16.8943 20.5248 16.8487 20.4213 16.7971C20.2809 16.7106 19.9688 16.5497 19.6962 16.4139C19.5401 16.3361 19.3828 16.2596 19.2521 16.1982C19.1342 16.1429 19.0062 16.0846 18.9283 16.0588C18.924 16.0573 18.919 16.0556 18.9133 16.0537C18.8405 16.0288 18.6596 15.9671 18.4695 15.9843C18.3484 15.9953 18.2201 16.0363 18.1026 16.1234C17.9891 16.2075 17.9106 16.316 17.8555 16.4257C17.8253 16.486 17.7283 16.6213 17.5941 16.7848C17.5054 16.8927 17.4198 16.9899 17.3621 17.0528C17.3315 17.0384 17.2977 17.023 17.261 17.0063C16.9596 16.869 16.4576 16.6404 15.823 16.0995C15.3288 15.6505 14.9888 15.1669 14.8369 14.914C14.8557 14.8924 14.8721 14.8719 14.8842 14.8568C14.8999 14.8371 14.9183 14.8133 14.9354 14.7913C14.9432 14.7812 14.9507 14.7715 14.9576 14.7628C15.0088 14.6971 15.0505 14.6465 15.0846 14.6125C15.2597 14.438 15.3488 14.2601 15.4075 14.1429C15.4093 14.1394 15.411 14.1359 15.4128 14.1325C15.5421 13.8747 15.5421 13.5918 15.4128 13.3341L15.3763 13.2615L15.3681 13.2532C15.3565 13.2285 15.3416 13.1955 15.3234 13.1539C15.2764 13.0464 15.2161 12.9014 15.1503 12.7413C15.11 12.6432 15.0676 12.539 15.0255 12.4357C14.9334 12.2094 14.843 11.9877 14.7813 11.8441C14.6707 11.5869 14.5152 11.3681 14.2722 11.258C14.0615 11.1626 13.8013 11.1916 13.8537 11.1916H13.853H13.8523H13.8516H13.8509H13.8502H13.8495H13.8488H13.8481H13.8474H13.8467H13.846H13.8453H13.8446H13.8439H13.8432H13.8425H13.8418H13.8411H13.8404H13.8397H13.839H13.8382H13.8375H13.8368H13.8361H13.8354H13.8347H13.8339H13.8332H13.8325H13.8318H13.831H13.8303H13.8296H13.8289H13.8281H13.8274H13.8267H13.8259H13.8252H13.8245H13.8237H13.823H13.8223H13.8215H13.8208H13.82H13.8193H13.8185H13.8178H13.817H13.8163H13.8155H13.8148H13.814H13.8133H13.8125H13.8118H13.811H13.8103H13.8095H13.8088H13.808H13.8072H13.8065H13.8057H13.805H13.8042H13.8034H13.8027H13.8019H13.8011H13.8003H13.7996H13.7988H13.798H13.7973H13.7965H13.7957H13.7949H13.7942H13.7934H13.7926H13.7918H13.791H13.7903H13.7895H13.7887H13.7879H13.7871H13.7863H13.7856H13.7848H13.784H13.7832H13.7824H13.7816H13.7808H13.78H13.7792H13.7784H13.7776H13.7768H13.7761H13.7753H13.7745H13.7737H13.7729H13.7721H13.7713H13.7705H13.7696H13.7688H13.768H13.7672H13.7664H13.7656H13.7648H13.764H13.7632H13.7624H13.7616H13.7608H13.76H13.7591H13.7583H13.7575H13.7567H13.7559H13.7551H13.7543H13.7534H13.7526H13.7518H13.751H13.7502H13.7493H13.7485H13.7477H13.7469H13.7461H13.7452H13.7444H13.7436H13.7428H13.7419H13.7411H13.7403H13.7395H13.7386H13.7378H13.737H13.7361H13.7353H13.7345H13.7337H13.7328H13.732H13.7312H13.7303H13.7295H13.7287H13.7278H13.727H13.7262H13.7253H13.7245H13.7236H13.7228H13.722H13.7211H13.7203H13.7194H13.7186H13.7178H13.7169H13.7161H13.7152H13.7144H13.7136H13.7127H13.7119H13.711H13.7102H13.7093H13.7085H13.7077H13.7068H13.706H13.7051H13.7043H13.7034H13.7026H13.7017H13.7009H13.7H13.6992H13.6983H13.6975H13.6966H13.6958H13.6949H13.6941H13.6932H13.6924H13.6915H13.6907H13.6898H13.689H13.6881H13.6873H13.6864H13.6856H13.6847H13.6839H13.683H13.6822H13.6813H13.6805H13.6796H13.6788H13.6779H13.677H13.6762H13.6753H13.6745H13.6736H13.6728H13.6719H13.6711H13.6702H13.6693H13.6685H13.6676H13.6668H13.6659H13.6651H13.6642H13.6634H13.6625H13.6616H13.6608H13.6599H13.6591H13.6582H13.6574H13.6565H13.6556H13.6548H13.6539H13.6531H13.6522H13.6514H13.6505H13.6496H13.6488H13.6479H13.6471H13.6462H13.6453H13.6445H13.6436H13.6428H13.6419H13.6411H13.6402H13.6393H13.6385H13.6376H13.6368H13.6359H13.6351H13.6342H13.6333H13.6325H13.6316H13.6308H13.6299H13.6291H13.6282H13.6274H13.6265H13.6256H13.6248H13.6239H13.6231H13.6222H13.6214H13.6205H13.6197H13.6188H13.618H13.6171H13.6162H13.6154H13.6145H13.6137H13.6128H13.612H13.6111H13.6103H13.6094H13.6086H13.6077H13.6069H13.606H13.6052H13.6043H13.6035H13.6026H13.6018H13.6009H13.6001H13.5992H13.5984H13.5975H13.5967H13.5958H13.595H13.5941H13.5933H13.5924H13.5916H13.5907H13.5899H13.5891H13.5882H13.5874H13.5865H13.5857H13.5848H13.584H13.5832H13.5823H13.5815H13.5806H13.5798H13.5789H13.5781H13.5773H13.5764H13.5756H13.5748H13.5739H13.5731H13.5722H13.5714H13.5706H13.5697H13.5689H13.5681H13.5672H13.5664H13.5656H13.5647H13.5639H13.5631H13.5622H13.5614H13.5606H13.5598H13.5589H13.5581H13.5573H13.5564H13.5556H13.5548H13.554H13.5531H13.5523H13.5515H13.5507H13.5499H13.549H13.5482H13.5474H13.5466H13.5458H13.5449H13.5441H13.5433H13.5425H13.5417H13.5409H13.54H13.5392H13.5384H13.5376H13.5368H13.536H13.5352H13.5344H13.5335H13.5327H13.5319H13.5311H13.5303H13.5295H13.5287H13.5279H13.5271H13.5263H13.5255H13.5247H13.5239H13.5231H13.5223H13.5215H13.5207H13.5199H13.5191H13.5183H13.5175H13.5167H13.5159H13.5152H13.5144H13.5136H13.5128H13.512H13.5112H13.5104H13.5096H13.5089H13.5081H13.5073H13.5065H13.5057H13.505H13.5042H13.5034H13.5026H13.5018H13.5011H13.5003H13.4995H13.4988H13.498H13.4972H13.4964H13.4957H13.4949H13.4941H13.4934H13.4926H13.4918H13.4911H13.4903H13.4896H13.4888H13.488H13.4873H13.4865H13.4858H13.485H13.4843H13.4835H13.4828H13.482H13.4813H13.4805H13.4798H13.479H13.4783H13.4775H13.4768H13.476H13.4753H13.4746H13.4738H13.4731H13.4724H13.4716H13.4709H13.4702H13.4694H13.4687H13.468H13.4672H13.4665H13.4658H13.4651H13.4643H13.4636H13.4629H13.4622H13.4615H13.4607H13.46H13.4593H13.4586H13.4579H13.4572H13.4565H13.4558H13.455H13.4543H13.4536H13.4529H13.4522H13.4515H13.4508H13.4501H13.4494H13.4487H13.448H13.4473H13.4467H13.446H13.4453H13.4446H13.4439C13.4387 11.1916 13.4332 11.1916 13.4274 11.1916C13.245 11.1911 12.786 11.1899 12.459 11.6243C12.4526 11.6328 12.4394 11.6484 12.421 11.6702C12.3374 11.7689 12.1471 11.9936 12.0042 12.2512C11.8121 12.5976 11.6561 13.0597 11.6561 13.675C11.6561 14.3128 11.8844 14.9058 12.1077 15.3349C12.3026 15.7095 12.5186 16.0059 12.639 16.1422C12.8476 16.4581 14.236 18.5337 16.3973 19.4385C16.7473 19.6114 17.0967 19.7359 17.3494 19.8259C17.4455 19.8601 17.5277 19.8894 17.5905 19.9144Z"
                          fill="#474A51" stroke="#474A51"/>
                </svg>
            </a>
        </div>
        <ul class="footer__mobile-list--menu">
            <li>
                <a href="mailto:zhgrader@yandex.ru?subject=%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B0%D1%8F%20%D0%BF%D0%BE%D0%B4%D0%B4%D0%B5%D1%80%D0%B6%D0%BA%D0%B0">Техническая поддержка</a>
            </li>
            <li>
                <a class="separator-links" href="/about/#requisites">Контакты</a>
                <a href="/about/">О Сервисе</a>
            </li>
            <li>
                <a href="/about/#faq">Часто задаваемые вопросы</a>
            </li>
            <li>
                <a href="/output/pdf/public-offer.pdf" target="_blank" rel="noopener">Публичная оферта</a>
            </li>
            <li>
                <a href="/output/pdf/privacy-policy.pdf" target="_blank" rel="noopener">Политика конфиденциальности</a>
            </li>
            <li>
                <a href="/output/pdf/personal-data-consent.pdf" target="_blank" rel="noopener">Согласие на обработку персональных данных</a>
            </li>
            <li>
                <a href="/output/pdf/investor-publication-consent.pdf" target="_blank" rel="noopener">Согласие на публикацию персональных данных для инвесторов</a>
            </li>
        </ul>
    </div>
    <div class="footer__social-links">
        <a href="https://t.me/share/url" data-footer-share="telegram" target="_blank" rel="noopener" aria-label="Поделиться страницей в Telegram">
            <svg id="telegram" width="48" height="48" viewBox="0 0 48 48" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <rect width="48" height="48" rx="24" fill="white"/>
                <path d="M28.362 34.1976L20.5155 27.9675C22.6065 26.0216 29.9578 19.1759 30.3109 18.8349L30.311 18.8348C30.4878 18.664 30.6334 18.4736 30.707 18.2691C30.7802 18.0662 30.8074 17.7475 30.5625 17.5054C30.3441 17.2895 30.0532 17.2858 29.8658 17.3164C29.6593 17.3501 29.4449 17.4424 29.2365 17.5734L29.2364 17.5733L29.2278 17.579L16.5594 25.9101L11.8338 24.2636L11.8219 24.2587C11.8083 24.2529 11.7865 24.2433 11.7591 24.2298C11.7032 24.2022 11.6296 24.1611 11.5565 24.1074C11.3971 23.9905 11.3139 23.8705 11.3005 23.7592L11.3005 23.7585C11.2979 23.7372 11.3059 23.6503 11.506 23.5084C11.5896 23.4492 11.6791 23.4015 11.7504 23.3681C11.7852 23.3517 11.8138 23.3397 11.8324 23.3322C11.8417 23.3284 11.8485 23.3259 11.8521 23.3245L11.8544 23.3236L11.855 23.3234L11.8553 23.3233L11.8643 23.3201L11.8749 23.3158L32.1238 15.0899L32.1333 15.086L32.1416 15.0822L32.1418 15.0821L32.1422 15.082L32.1424 15.0819L32.1426 15.0817L32.1428 15.0817L32.1517 15.0778C32.1605 15.074 32.1746 15.0681 32.1933 15.0606C32.2308 15.0455 32.2858 15.0246 32.3521 15.0028C32.4891 14.9576 32.6547 14.9151 32.8068 14.9031C32.9707 14.8903 33.0423 14.9188 33.0602 14.9309L33.0606 14.9312C33.0631 14.9328 33.0672 14.9356 33.0738 14.9499C33.0814 14.9664 33.0949 15.0057 33.0988 15.0805L29.5132 33.7993C29.5028 33.8204 29.4872 33.8497 29.4664 33.8837C29.4166 33.965 29.3439 34.0612 29.2487 34.1401C29.1564 34.2166 29.047 34.2738 28.9125 34.2929C28.7877 34.3106 28.6087 34.3 28.362 34.1976ZM29.5287 33.7659C29.5288 33.7656 29.5285 33.7663 29.5278 33.7681L29.5281 33.7673L29.5287 33.7659Z"
                      fill="#474A51" stroke="#474A51"/>
            </svg>
        </a>
        <a href="https://wa.me/" data-footer-share="whatsapp" target="_blank" rel="noopener" aria-label="Поделиться страницей в WhatsApp">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="48" height="48" rx="24" fill="white"/>
                <path d="M14.2387 29.6559L14.2929 29.456L14.1898 29.2763C13.1779 27.5115 12.5878 25.4093 12.5878 23.3125C12.5878 16.6778 17.9818 11.3 24.6439 11.3C27.8473 11.3 30.8816 12.5595 33.1593 14.8292C35.4299 17.092 36.6138 20.1137 36.7 23.2318C36.6963 29.8634 31.3037 35.2375 24.6439 35.2375C22.6214 35.2375 20.6808 34.734 18.9045 33.8068L18.7332 33.7174L18.5463 33.7664L12.7087 35.2973L14.2387 29.6559ZM26.4783 29.6395L26.5097 29.652L26.5425 29.6602C27.3133 29.8522 28.0826 29.8537 28.6717 29.7567C29.0792 29.6971 29.6079 29.4595 30.0546 29.1666C30.4933 28.8791 30.9806 28.4555 31.1704 27.9511C31.3187 27.557 31.3947 27.1831 31.4203 26.8635C31.4451 26.5554 31.4258 26.2616 31.3522 26.0414L31.3147 25.9293L31.231 25.8459C31.1349 25.7501 31.0171 25.6788 30.9117 25.6204C30.8063 25.5621 30.6712 25.4946 30.5104 25.4145C30.3224 25.2968 29.857 25.0561 29.4327 24.8447C29.1999 24.7287 28.9657 24.6148 28.7719 24.5238C28.5909 24.4388 28.4135 24.3587 28.3137 24.3255C28.3068 24.3233 28.2993 24.3207 28.2913 24.318C28.1785 24.28 27.9557 24.2048 27.7268 24.2255C27.5856 24.2383 27.4381 24.2857 27.3028 24.386C27.1714 24.4833 27.0764 24.6119 27.0068 24.7508C26.9473 24.8694 26.7844 25.0922 26.5843 25.3359C26.3923 25.5698 26.2064 25.7746 26.1349 25.8459C26.1202 25.8605 26.1088 25.8708 26.1004 25.878C26.082 25.8727 26.0522 25.8619 26.0084 25.84C25.9479 25.8099 25.8745 25.7765 25.7893 25.7378C25.3376 25.5323 24.5514 25.1748 23.5693 24.3371C22.6854 23.5349 22.1179 22.6588 21.9463 22.3327C21.9636 22.3146 21.9845 22.2935 22.0115 22.2667C22.0559 22.2224 22.0991 22.1692 22.1307 22.1296C22.1533 22.1012 22.1792 22.0679 22.2043 22.0355C22.2163 22.02 22.2281 22.0048 22.2393 21.9905C22.3162 21.892 22.3871 21.8049 22.4505 21.7417C22.6839 21.5091 22.8023 21.2729 22.8897 21.0987L22.8957 21.0868C23.0543 20.7707 23.0543 20.4294 22.8957 20.1133L22.8593 20.0407L22.8453 20.0268C22.8443 20.0248 22.8432 20.0226 22.8421 20.0203C22.821 19.9772 22.7921 19.9137 22.756 19.8311C22.6844 19.6671 22.5929 19.4474 22.4942 19.207C22.433 19.0577 22.3688 18.9001 22.3054 18.7446C22.1677 18.4066 22.0339 18.0781 21.9422 17.865C21.7878 17.5058 21.5884 17.2432 21.3051 17.1149C21.1661 17.0519 21.0377 17.0361 20.9401 17.0336C20.8936 17.0324 20.8523 17.0343 20.8259 17.0356C20.8208 17.0358 20.8163 17.0361 20.8123 17.0362C20.7913 17.0373 20.7858 17.0375 20.7805 17.0375H20.7795H20.7784H20.7774H20.7764H20.7753H20.7743H20.7732H20.7722H20.7711H20.7701H20.769H20.768H20.7669H20.7659H20.7648H20.7638H20.7627H20.7616H20.7606H20.7595H20.7584H20.7574H20.7563H20.7552H20.7542H20.7531H20.752H20.7509H20.7498H20.7487H20.7477H20.7466H20.7455H20.7444H20.7433H20.7422H20.7411H20.74H20.7389H20.7378H20.7367H20.7356H20.7345H20.7334H20.7323H20.7312H20.73H20.7289H20.7278H20.7267H20.7256H20.7244H20.7233H20.7222H20.7211H20.7199H20.7188H20.7177H20.7165H20.7154H20.7143H20.7131H20.712H20.7109H20.7097H20.7086H20.7074H20.7063H20.7051H20.704H20.7028H20.7017H20.7005H20.6994H20.6982H20.6971H20.6959H20.6947H20.6936H20.6924H20.6912H20.6901H20.6889H20.6877H20.6866H20.6854H20.6842H20.683H20.6819H20.6807H20.6795H20.6783H20.6772H20.676H20.6748H20.6736H20.6724H20.6712H20.67H20.6688H20.6677H20.6665H20.6653H20.6641H20.6629H20.6617H20.6605H20.6593H20.6581H20.6569H20.6557H20.6545H20.6533H20.6521H20.6509H20.6496H20.6484H20.6472H20.646H20.6448H20.6436H20.6424H20.6412H20.6399H20.6387H20.6375H20.6363H20.6351H20.6338H20.6326H20.6314H20.6302H20.6289H20.6277H20.6265H20.6253H20.624H20.6228H20.6216H20.6203H20.6191H20.6179H20.6166H20.6154H20.6141H20.6129H20.6117H20.6104H20.6092H20.6079H20.6067H20.6055H20.6042H20.603H20.6017H20.6005H20.5992H20.598H20.5967H20.5955H20.5942H20.593H20.5917H20.5905H20.5892H20.588H20.5867H20.5855H20.5842H20.5829H20.5817H20.5804H20.5792H20.5779H20.5767H20.5754H20.5741H20.5729H20.5716H20.5703H20.5691H20.5678H20.5666H20.5653H20.564H20.5628H20.5615H20.5602H20.5589H20.5577H20.5564H20.5551H20.5539H20.5526H20.5513H20.5501H20.5488H20.5475H20.5462H20.545H20.5437H20.5424H20.5411H20.5399H20.5386H20.5373H20.536H20.5348H20.5335H20.5322H20.5309H20.5296H20.5284H20.5271H20.5258H20.5245H20.5233H20.522H20.5207H20.5194H20.5181H20.5168H20.5156H20.5143H20.513H20.5117H20.5104H20.5092H20.5079H20.5066H20.5053H20.504H20.5027H20.5015H20.5002H20.4989H20.4976H20.4963H20.495H20.4937H20.4925H20.4912H20.4899H20.4886H20.4873H20.486H20.4847H20.4835H20.4822H20.4809H20.4796H20.4783H20.477H20.4757H20.4745H20.4732H20.4719H20.4706H20.4693H20.468H20.4667H20.4655H20.4642H20.4629H20.4616H20.4603H20.459H20.4577H20.4565H20.4552H20.4539H20.4526H20.4513H20.45H20.4487H20.4475H20.4462H20.4449H20.4436H20.4423H20.441H20.4397H20.4385H20.4372H20.4359H20.4346H20.4333H20.4321H20.4308H20.4295H20.4282H20.4269H20.4256H20.4244H20.4231H20.4218H20.4205H20.4192H20.418H20.4167H20.4154H20.4141H20.4129H20.4116H20.4103H20.409H20.4077H20.4065H20.4052H20.4039H20.4026H20.4014H20.4001H20.3988H20.3976H20.3963H20.395H20.3937H20.3925H20.3912H20.3899H20.3887H20.3874H20.3861H20.3849H20.3836H20.3823H20.3811H20.3798H20.3785H20.3773H20.376H20.3747H20.3735H20.3722H20.3709H20.3697H20.3684H20.3672H20.3659H20.3646H20.3634H20.3621H20.3609H20.3596H20.3584H20.3571H20.3559H20.3546H20.3534H20.3521H20.3509H20.3496H20.3484H20.3471H20.3459H20.3446H20.3434H20.3421H20.3409H20.3396H20.3384H20.3371H20.3359H20.3347H20.3334H20.3322H20.331H20.3297H20.3285H20.3272H20.326H20.3248H20.3235H20.3223H20.3211H20.3199H20.3186H20.3174H20.3162H20.3149H20.3137H20.3125H20.3113H20.3101H20.3088H20.3076H20.3064H20.3052H20.304H20.3028H20.3015H20.3003H20.2991H20.2979H20.2967H20.2955H20.2943H20.2931H20.2919H20.2907H20.2895H20.2883H20.2871H20.2859H20.2847H20.2835H20.2823H20.2811H20.2799H20.2787H20.2775H20.2763H20.2751H20.2739H20.2727H20.2716H20.2704H20.2692H20.268H20.2668H20.2656H20.2645H20.2633H20.2621H20.2609H20.2598H20.2586H20.2574H20.2563H20.2551H20.2539H20.2528H20.2516H20.2504H20.2493H20.2481H20.247H20.2458H20.2447H20.2435H20.2424H20.2412H20.2401H20.2389H20.2378H20.2366H20.2355H20.2343H20.2332H20.2321H20.2309H20.2298H20.2287H20.2275H20.2264H20.2253H20.2241H20.223H20.2219H20.2208H20.2196H20.2185H20.2174H20.2163H20.2152H20.2141H20.213H20.2119H20.2107H20.2096H20.2085H20.2074H20.2063H20.2052H20.2041H20.203H20.2019H20.2009H20.1998H20.1987H20.1976H20.1965H20.1954H20.1943H20.1933H20.1922H20.1911H20.19H20.189H20.1879H20.1868H20.1858H20.1847H20.1836H20.1826H20.1815H20.1805H20.1794H20.1783H20.1773H20.1762H20.1752H20.1741H20.1731H20.1721H20.171H20.17H20.1689H20.1679H20.1669H20.1659C20.1594 17.0375 20.1527 17.0375 20.1458 17.0375C19.8785 17.0371 19.3027 17.0363 18.8883 17.5869C18.8714 17.6093 18.843 17.643 18.8064 17.6864C18.6709 17.8471 18.4227 18.1416 18.2249 18.4982C17.956 18.9831 17.7341 19.6348 17.7341 20.5125C17.7341 21.4129 18.0576 22.2611 18.3833 22.8871C18.6776 23.4528 19.0005 23.8868 19.1574 24.0609C19.4377 24.4852 21.4958 27.5925 24.6997 28.9302C25.2122 29.1842 25.7098 29.3614 26.0885 29.4962C26.2396 29.55 26.3717 29.597 26.4783 29.6395ZM26.1239 25.883C26.1239 25.883 26.1232 25.883 26.1217 25.8828L26.1239 25.883ZM22.8609 20.0569C22.8605 20.0563 22.8601 20.0556 22.8595 20.0545C22.8647 20.0627 22.8653 20.0647 22.8609 20.0569Z"
                      fill="#474A51" stroke="#474A51"/>
            </svg>
        </a>
    </div>
    <p class="footer__mobile-copyright-text">© «Желтый грейдер», 2024</p>
    <p class="footer__copyright-text">© «Желтый грейдер», 2024</p>
</footer>
<div class="cookie-consent" id="cookieConsent" role="dialog" aria-labelledby="cookieConsentTitle" aria-describedby="cookieConsentText" hidden>
    <div class="cookie-consent__content">
        <h2 id="cookieConsentTitle">Использование cookie</h2>
        <p id="cookieConsentText">Мы используем обязательные cookie для работы сайта и, с вашего разрешения, аналитические cookie для улучшения сервиса.</p>
        <a href="/output/pdf/privacy-policy.pdf" target="_blank" rel="noopener">Политика конфиденциальности</a>
        <label class="cookie-consent__option"><input type="checkbox" id="cookieAnalytics" checked> Разрешить аналитические cookie</label>
    </div>
    <div class="cookie-consent__actions">
        <button type="button" class="cookie-consent__button cookie-consent__button--secondary" id="cookieReject">Только обязательные</button>
        <button type="button" class="cookie-consent__button" id="cookieAccept">Принять выбранные</button>
    </div>
</div>
<script>
(function () {
    var key = 'grader_cookie_consent_v1';
    var banner = document.getElementById('cookieConsent');
    if (!banner) return;
    var stored = null;
    try { stored = JSON.parse(localStorage.getItem(key) || 'null'); } catch (e) {}
    if (!stored || stored.required !== true) banner.hidden = false;
    function save(analytics) {
        try { localStorage.setItem(key, JSON.stringify({required: true, analytics: !!analytics, at: new Date().toISOString()})); } catch (e) {}
        banner.hidden = true;
        document.documentElement.dataset.analyticsCookies = analytics ? 'allowed' : 'blocked';
        document.dispatchEvent(new CustomEvent('grader:cookieConsent', {detail: {analytics: !!analytics}}));
    }
    document.getElementById('cookieAccept').addEventListener('click', function () {
        save(document.getElementById('cookieAnalytics').checked);
    });
    document.getElementById('cookieReject').addEventListener('click', function () { save(false); });
})();
</script>
<script>
    (function () {
        var url = window.location.href;
        var title = document.title;
        document.querySelectorAll('.footer-share').forEach(function (root) {
            var trigger = root.querySelector('.footer-share__trigger');
            var menu = root.querySelector('.footer-share__menu');
            var close = function () {
                menu.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            };
            trigger.addEventListener('click', function () {
                document.querySelectorAll('.footer-share').forEach(function (other) {
                    if (other !== root && other._closeShareMenu) other._closeShareMenu();
                });
                var open = menu.classList.toggle('is-open');
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            root.querySelectorAll('[data-share-service]').forEach(function (item) {
                item.addEventListener('click', function () {
                    var service = item.dataset.shareService;
                    var target = '';
                    if (service === 'max') target = 'https://max.ru/share?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
                    if (service === 'vk') target = 'https://vk.com/share.php?url=' + encodeURIComponent(url);
                    if (service === 'telegram') target = 'https://t.me/share/url?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
                    if (service === 'whatsapp') target = 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + url);
                    if (service === 'ok') target = 'https://connect.ok.ru/offer?url=' + encodeURIComponent(url) + '&title=' + encodeURIComponent(title);
                    if (service === 'copy') {
                        var copied = navigator.clipboard && navigator.clipboard.writeText
                            ? navigator.clipboard.writeText(url)
                            : Promise.reject();
                        copied.catch(function () {
                            var textarea = document.createElement('textarea');
                            textarea.value = url;
                            textarea.style.position = 'fixed';
                            textarea.style.left = '-9999px';
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            textarea.remove();
                        });
                        return;
                    }
                    window.open(target, '_blank', 'noopener,noreferrer');
                    close();
                });
            });
            root.addEventListener('click', function (event) { event.stopPropagation(); });
            root._closeShareMenu = close;
        });
        document.addEventListener('click', function () {
            document.querySelectorAll('.footer-share').forEach(function (root) { if (root._closeShareMenu) root._closeShareMenu(); });
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') document.querySelectorAll('.footer-share').forEach(function (root) { if (root._closeShareMenu) root._closeShareMenu(); });
        });
    }());
    document.querySelectorAll('[data-footer-share]').forEach(function (link) {
        link.addEventListener('click', function () {
            var pageUrl = encodeURIComponent(window.location.href);
            link.href = link.dataset.footerShare === 'telegram'
                ? 'https://t.me/share/url?url=' + pageUrl
                : 'https://wa.me/?text=' + pageUrl;
        });
    });
</script>
</body>
</html>


