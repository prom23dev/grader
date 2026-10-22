<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 */
global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
    return "";

$strReturn = '';
$goRight = '<svg class="breadcrumb-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M17.5608 13.0608L10.0608 20.5608L7.93945 18.4395L14.3788 12.0001L7.93945 5.56077L10.0608 3.43945L17.5608 10.9395C18.1466 11.5252 18.1466 12.475 17.5608 13.0608Z" fill="#474A51"/>
</svg>';
$itemSize = count($arResult);
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

if (preg_match('#^/projects/ongoing(?:/|$)#', $requestPath)) {
    return '<li><a href="/" title="Главная" itemprop="item"><span itemprop="name">Главная</span></a></li>'
        . '<li>'.$goRight.'<a href="/projects/" title="Проекты" itemprop="item"><span itemprop="name">Проекты</span></a></li>'
        . '<li>'.$goRight.'Действующие проекты</li>';
}

$isProjectPage = (bool)preg_match('#^/projects(?:/|$)#', $requestPath);
$isCreatePage = in_array(rtrim($requestPath, '/'), ['/projects/create', '/projects/create.php'], true);
$hasProjectsCrumb = false;
foreach ($arResult as $crumb) {
    if (mb_strtolower(trim((string)$crumb['TITLE'])) === 'проекты') {
        $hasProjectsCrumb = true;
        break;
    }
}
for($index = 0; $index < $itemSize; $index++)
{
    if ($requestPath === '/projects/result/' && mb_strtolower(trim((string)$arResult[$index]['TITLE'])) === 'завершенный проект') {
        $arResult[$index]['TITLE'] = 'Завершенные проекты';
    }
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    $arrow = ($index > 0?  $goRight: '');

    if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
    {
        $strReturn .= '<li>'.$arrow.'<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item"><span itemprop="name">'.$title.'</a></li>';
    }
    else
    {
        $strReturn .= '<li>'.$arrow.$title.'</li>';
    }
    if ($isProjectPage && !$hasProjectsCrumb && $index === 0) {
        $strReturn .= '<li>'.$goRight.'<a href="/projects/" title="Все проекты" itemprop="item"><span itemprop="name">Все проекты</span></a></li>';
    }
}
if ($isCreatePage) {
    $strReturn .= '<li>'.$goRight.'Создать проект</li>';
}

return $strReturn;
?>

