<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $USER;
if (!$USER->IsAuthorized()) {
    header("Location:/");
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
    die();
}
$APPLICATION->SetTitle("");
CModule::IncludeModule("search");?>
<?
global $userProjects;
$userProjects = array('CREATED_BY'=> $USER->GetId());
if ($_REQUEST['q']) {
    $obSearch = new CSearch;
    $obSearch->SetOptions(array(//мы добавили еще этот параметр, чтобы не ругался на форматирование запроса
        'ERROR_ON_EMPTY_STEM' => false,
    ));
    $obSearch->Search(array(
        'QUERY' => $_REQUEST['q'],
        'SITE_ID' => SITE_ID,
        'MODULE_ID' => 'iblock',
        'PARAM2' => 1
    ));
    if (!$obSearch->selectedRowsCount()) {//и делаем резапрос, если не найдено с морфологией...
        $obSearch->Search(array(
            'QUERY' => $_REQUEST['q'],
            'SITE_ID' => SITE_ID,
            'MODULE_ID' => 'iblock',
            'PARAM2' => 1
        ), array(), array('STEMMING' => false));//... уже с отключенной морфологией
    }
    $arSearchItemsIds = array();
    while ($row = $obSearch->fetch()) {
        //в $row['ITEM_ID'] у вас будут выводиться ID нужных элементов
        $arSearchItemsIds[] = $row['ITEM_ID'];
    }
    if (!$arSearchItemsIds) {
        $arSearchItemsIds = [0];
    }
    $userProjects['ID'] = $arSearchItemsIds;

}

?>
<?$APPLICATION->IncludeComponent("bitrix:news.list", "my-projects", Array(
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
		"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
		"AJAX_MODE" => "N",	// Включить режим AJAX
		"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
		"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
		"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
		"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
		"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
		"CACHE_GROUPS" => "Y",	// Учитывать права доступа
		"CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CHECK_DATES" => "N",	// Показывать только активные на данный момент элементы
		"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
		"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
		"DISPLAY_DATE" => "Y",	// Выводить дату элемента
		"DISPLAY_NAME" => "Y",	// Выводить название элемента
		"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
		"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
		"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
		"FIELD_CODE" => array(	// Поля
			0 => "ID",
			1 => "NAME",
			2 => "DATE_CREATE",
			3 => "CREATED_BY",
			4 => "",
		),
		"FILE_404" => "",	// Страница для показа (по умолчанию /404.php)
		"FILTER_NAME" => "userProjects",	// Фильтр
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
		"IBLOCK_ID" => PROJECTS_IBLOCK_ID,	// Код информационного блока
		"IBLOCK_TYPE" => "company",	// Тип информационного блока (используется только для проверки)
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
		"INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "10",	// Количество новостей на странице
		"PAGER_BASE_LINK_ENABLE" => "N",	// Включить обработку ссылок
		"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
		"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
		"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
		"PAGER_TEMPLATE" => "pagenav",	// Шаблон постраничной навигации
		"PAGER_TITLE" => "Новости",	// Название категорий
		"PARENT_SECTION" => "",	// ID раздела
		"PARENT_SECTION_CODE" => "",	// Код раздела
		"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
		"PROPERTY_CODE" => array(	// Свойства
			0 => "PR_ADDRESS",
			1 => "PR_WORK_TYPE",
			2 => "PR_STATUS",
		),
		"SET_BROWSER_TITLE" => "Y",	// Устанавливать заголовок окна браузера
		"SET_LAST_MODIFIED" => "N",	// Устанавливать в заголовках ответа время модификации страницы
		"SET_META_DESCRIPTION" => "Y",	// Устанавливать описание страницы
		"SET_META_KEYWORDS" => "Y",	// Устанавливать ключевые слова страницы
		"SET_STATUS_404" => "Y",	// Устанавливать статус 404
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"SHOW_404" => "Y",	// Показ специальной страницы
		"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
		"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
		"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
		"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
		"STRICT_SECTION_CHECK" => "N",	// Строгая проверка раздела для показа списка
	),
	false
);?><? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>