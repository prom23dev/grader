<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$consentIdPrefix = isset($consentIdPrefix) && $consentIdPrefix !== ''
    ? preg_replace('/[^a-zA-Z0-9_-]/', '-', $consentIdPrefix)
    : 'form';
?>
<div class="agreements-list">
    <div class="agreement-block">
        <label for="<?= $consentIdPrefix ?>-accept-offer">
            <input
                required
                type="checkbox"
                id="<?= $consentIdPrefix ?>-accept-offer"
                name="accept_offer"
                value="agreement"
            />
            <span class="checkmark"></span>
            Принимаю условия
            <a href="/output/pdf/public-offer.pdf" target="_blank" rel="noopener">Оферты о заключении договора</a>
        </label>
    </div>
    <div class="agreement-block">
        <label for="<?= $consentIdPrefix ?>-accept-personal-data">
            <input
                required
                type="checkbox"
                id="<?= $consentIdPrefix ?>-accept-personal-data"
                name="accept_personal_data"
                value="agreement"
            />
            <span class="checkmark"></span>
            Выражаю
            <a href="/output/pdf/personal-data-consent.pdf" target="_blank" rel="noopener">Согласие на обработку персональных данных</a>
            в соответствии с условиями
            <a href="/output/pdf/privacy-policy.pdf" target="_blank" rel="noopener">Политики конфиденциальности</a>
        </label>
    </div>
</div>
