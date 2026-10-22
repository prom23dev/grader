<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 * @global CUser $USER
 */
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss(SITE_DIR."personal/style.css");
CJSCore::Init();
?>
<div class="profile">
    <h1><?=$USER->GetLogin()?></h1>
    <div class="account_block">
        <?php $personalPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/') . '/'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/personal/profile-tabs.php'; ?>
        <ul class="account__menu account__menu--actions">


        </ul>
    </div>
</div>
<form action="<?=$arResult["AUTH_URL"]?>" hidden>
	<table width="95%">
		<tr>
			<td align="center">
				<?=$arResult["USER_NAME"]?><br />
				[<?=$arResult["USER_LOGIN"]?>]<br />
				<a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
			</td>
		</tr>
		<tr>
			<td align="center">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="logout" value="yes" data-user-id="<?=$USER->getId()?>" />
			<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
			</td>
		</tr>
	</table>
</form>
    <dialog class="dialog" id="leaveDialog">
        <form method="dialog">
            <div class="leave-dialog__content">
                <h1>Выход из личного кабинета</h1>
                <div>
                    <p>Вы действительно хотите выйти из личного кабинета?</p>
                </div>
                <div class="leave-dialog__choose-block">
                    <button class="dialog-agree-button" id="leaveYes" type="button">Да</button>
                    <button onclick="closeDialog(leaveDialog)" class="dialog-disagree-button" id="leaveNo" type="button">Нет</button>
                </div>
            </div>
        </form>
    </dialog>
