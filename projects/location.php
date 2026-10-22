<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->IncludeComponent("bitrix:sale.location.selector.search", "grader-location", Array(
	"COMPONENT_TEMPLATE" => ".default",
		"CODE" => "",	// Символьный код местоположения
		"INPUT_NAME" => "LOCATION",	// Имя поля ввода
		"PROVIDE_LINK_BY" => "id",	// Сохранять связь через
		"JSCONTROL_GLOBAL_ID" => "",
		"JS_CALLBACK" => "",	// Javascript-функция обратного вызова
		"FILTER_BY_SITE" => "Y",	// Фильтровать по сайту
		"SHOW_DEFAULT_LOCATIONS" => "Y",	// Отображать местоположения по-умолчанию
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
		"FILTER_SITE_ID" => "s1",	// Cайт
		"INITIALIZE_BY_GLOBAL_EVENT" => "",	// Инициализировать компонент только при наступлении указанного javascript-события на объекте window.document
		"SUPPRESS_ERRORS" => "N",	// Не показывать ошибки, если они возникли при загрузке компонента
	),
	false
);?>