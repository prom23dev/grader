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
$this->setFrameMode(true);
?>
<section class="profile">
    <div class="notifications__block">
        <div class="notifications__heading">
            <h1>Уведомления</h1>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/personal/profile-tabs.php'; ?>
        <? if (!empty($arResult['ITEMS'])): ?>
            <div class="notifications__clear-all-wrap">
                <button type="button" class="notifications__clear-all" id="clearAllNotifications" data-sessid="<?=bitrix_sessid()?>">Очистить все</button>
            </div>
        <? endif; ?>
        <? if (!empty($arResult['ITEMS'])): ?>
            <!--    Уведомления есть-->
            <div class="notifications__list" id="notificationsList">
                <? foreach ($arResult['ITEMS'] as $myNotification):
                    $isUnread = ($myNotification['PROPERTIES']['VIEWED']['VALUE'] ?? 'N') !== 'Y';
                    $notificationDate = $myNotification['DATE_CREATE'] ? date("d.m.Y", strtotime($myNotification['DATE_CREATE'])) : '';
                ?>
                    <div class="notifications__list-item<?= $isUnread ? ' is-unread' : '' ?>" data-notification-id="<?=$myNotification['ID']?>">
                        <div class="notifications__list-item__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="notifications__list-item__content">
                            <? if ($notificationDate || $isUnread): ?>
                                <div class="notifications__list-item__top">
                                    <? if ($notificationDate): ?><p class="notifications__list-item__date"><?=$notificationDate?></p><? endif; ?>
                                    <? if ($isUnread): ?><span class="notifications__list-item__dot" title="Новое"></span><? endif; ?>
                                </div>
                            <? endif; ?>
                            <p class="notifications__list-item__text"><?=$myNotification['NAME']?></p>
                        </div>
                        <button type="button" class="notifications__list-item__remove" data-notification-id="<?=$myNotification['ID']?>" data-sessid="<?=bitrix_sessid()?>" title="Удалить уведомление" aria-label="Удалить уведомление">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 6L18 18" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                <? endforeach; ?>
            </div>
        <? else: ?>
            <!--    Уведомлений нет-->
            <div class="notifications__not-yet-notification-block">
                <h3>Пока здесь нет ни одного уведомления.</h3>
                <div class="create-project__create-button-block">
                        <button onclick="redirectToCreateProject()" class="create-project__create-button">Создать проект</button>
                </div>
            </div>
        <? endif; ?>
    </div>
</section>