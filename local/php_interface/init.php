<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;

require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/geoLocation.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/projectDonations.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/objections.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/headerAnnounce.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/notificationsEvent.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/emailToLogin.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/emailSendEvent.php");
require_once "constants.php";
global $APPLICATION;

// Прогревает резолв текущего города (cookie/IP) заранее — результат кэшируется
// статической переменной внутри resolveCurrentCity() и повторно используется
// в шаблоне header-menu без повторного похода за GeoIP.
if (!defined('ADMIN_SECTION') || ADMIN_SECTION !== true) {
    resolveCurrentCity();
}
define("LOG_FILENAME", $_SERVER['DOCUMENT_ROOT'] . '/log.txt');

/**
 * Ресайз изображения с проверкой атрибутов
 * @param $photo id картинки или массив содержащий id
 * @param $wi ширина
 * @param $hi высота
 * @param $mode тип масштабирования при ресайзе (обрезать изображение или пропорционально уменьшать)
 * @return mixed
 */
function resizeImage($photo, $wi, $hi, $mode = false)
{
    if (is_array($photo)) $photo = $photo["ID"];
    if ($wi > 0 && $hi > 0) {
        $arPhoto = CFile::ResizeImageGet($photo, array("width" => $wi, "height" => $hi), ($mode ? BX_RESIZE_IMAGE_EXACT : BX_RESIZE_IMAGE_PROPORTIONAL), true, false, false, 100);
        $src = $arPhoto["src"];
    } else {
        $arPhoto = CFile::GetFileArray($photo);
        $src = $arPhoto["SRC"];
    }
    return $src;
}

/**
 * Функция для перехода на предыдущую страницу
 */
function urlBackward($url): string
{
    $currentUrl = $url;
    $arrUrl = explode('/', $currentUrl);
    $arrUrlCleared = array_filter($arrUrl, 'trim');
    array_pop($arrUrlCleared);
    array_push($arrUrlCleared, '');
    array_unshift($arrUrlCleared, '');
    $newUrl = implode('/', $arrUrlCleared);
    return $newUrl;
}

/**
 * Функция для генерации пароля
 */
function gen_password($length = 8): string
{
    $password = '';
    $arr = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
    );

    for ($i = 0; $i < $length; $i++) {
        $password .= $arr[random_int(0, count($arr) - 1)];
    }
    return $password;
}

function periodDayOrMonth($num): string
{
    $num = intval($num);
    if ($num > 28) {
        $monthNum = round($num / 30);
        if ($monthNum == 1){
            return $monthNum.' месяц';
        } else if (in_array($monthNum,[2,3,4])){
            return $monthNum. ' месяца';

        } else {
            return $monthNum.' месяцев';
        }
    } else {
        if ($num % 10 == 1 && $num % 100 != 11) {
            return $num.' день';
        } elseif (in_array($num % 10, [2, 3, 4]) && !in_array($num % 100, range(12, 14))) {
            return $num.' дня';
        } else {
            return $num.' дней';
        }
    }
}
