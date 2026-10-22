<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Loader;
use \Bitrix\Sale\Location;
Loader::includeModule('sale');

if (preg_match('/район/',$_POST['NAME'])) {
    $districtId = $_POST['ID'];

    $res = \Bitrix\Sale\Location\LocationTable::getList(array(
        'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, 'TYPE_CODE'=>'CITY', 'PARENT_ID'=>$districtId),
        'select' => array('*','ID'=>'ID','NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE'),
        'order' => array('NAME_RU' => 'ASC'),
    ));
    while ($locality = $res->fetch()) {
        $localities[] = array("ID"=>$locality['ID'], "NAME"=>$locality['NAME_RU']);
    }
    print_r(json_encode($localities));

} else {

$regionId = $_POST['ID'];

$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, 'TYPE_CODE'=>'SUBREGION', 'PARENT_ID'=>$regionId),
    'select' => array('*','ID'=>'ID','NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE'),
    'order' => array('NAME_RU' => 'ASC'),
));
while ($district = $res->fetch()) {
    $allDistricts[] = array("ID"=>$district['ID'], "NAME"=>$district['NAME_RU']);
}
print_r(json_encode($allDistricts));
}
