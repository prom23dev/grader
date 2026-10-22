<?php
global $USER;
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_DIR . "personal/style.css");
$profilePath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/') . '/';
$profileNotificationCount = 0;
if (class_exists('CModule') && CModule::IncludeModule('iblock') && class_exists('\Bitrix\Iblock\Elements\ElementNotifyTable')) {
    $profileNotificationCount = \Bitrix\Iblock\Elements\ElementNotifyTable::getCount([
        'ACTIVE' => 'Y', 'USER_ID.VALUE' => $USER->GetID(), '!VIEWED.VALUE' => 'Y',
    ]);
}
$profileNotificationCountLabel = $profileNotificationCount > 99 ? '99+' : $profileNotificationCount;
?>
<nav class="projects-tabs profile-tabs" aria-label="Разделы личного кабинета">
    <a class="projects-tabs__item<?= in_array($profilePath, ['/personal/', '/personal/profile/'], true) ? ' is-active' : '' ?>" href="/personal/profile/">Мой профиль</a>
    <a class="projects-tabs__item<?= $profilePath === '/personal/my-projects/' ? ' is-active' : '' ?>" href="/personal/my-projects/">Мои проекты</a>
    <a class="projects-tabs__item<?= $profilePath === '/personal/notifications/' ? ' is-active' : '' ?>" href="/personal/notifications/">Уведомления<?php if ($profileNotificationCount): ?><span class="account__notification-count"><?= $profileNotificationCountLabel ?></span><?php endif; ?></a>
    <a class="projects-tabs__item<?= $profilePath === '/personal/settings/' ? ' is-active' : '' ?>" href="/personal/settings/">Настройки</a>
</nav>
