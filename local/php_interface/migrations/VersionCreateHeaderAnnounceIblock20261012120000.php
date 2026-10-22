<?php

namespace Sprint\Migration;

class VersionCreateHeaderAnnounceIblock20261012120000 extends Version
{
    protected $author = "";

    protected $description = "Иблок «Объявления шапки» (бегущая строка в header.php) со свойствами ссылки и гео-привязки к HL-блокам GeoRegion/GeoDistrict/GeoSettlement";

    protected $moduleVersion = "5.15.0";

    const OPTION_IBLOCK_ID = 'grader_header_announce_iblock_id';

    public function up()
    {
        $helper = $this->getHelperManager();

        $iblockId = $helper->Iblock()->addIblockIfNotExists([
            'CODE' => 'header_announce',
            'IBLOCK_TYPE_ID' => 'services',
            'NAME' => 'Объявления шапки',
            'LID' => ['s1'],
            'GROUP_ID' => ['1' => 'X'],
            'VERSION' => 2,
        ]);
        \Bitrix\Main\Config\Option::set('main', self::OPTION_IBLOCK_ID, $iblockId);

        // Текст объявления хранится в NAME элемента, период показа — в
        // стандартных ACTIVE_FROM/ACTIVE_TO элемента (не отдельные свойства).

        $helper->Iblock()->saveProperty($iblockId, [
            'CODE' => 'ANNOUNCE_LINK',
            'NAME' => 'Ссылка (необязательно)',
            'PROPERTY_TYPE' => 'S',
            'SORT' => 100,
        ]);

        // Гео-привязка: ID элемента соответствующего HL-блока. Пустое
        // значение на любом из трёх уровней = объявление показывается всем
        // городам (та же логика, что в getGeoName() из geoLocation.php).
        $helper->Iblock()->saveProperty($iblockId, [
            'CODE' => 'PR_ANNOUNCE_REGION',
            'NAME' => 'Регион (GeoRegion ID)',
            'PROPERTY_TYPE' => 'S',
            'SORT' => 200,
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'CODE' => 'PR_ANNOUNCE_DISTRICT',
            'NAME' => 'Район (GeoDistrict ID)',
            'PROPERTY_TYPE' => 'S',
            'SORT' => 210,
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'CODE' => 'PR_ANNOUNCE_SETTLEMENT',
            'NAME' => 'Населённый пункт (GeoSettlement ID)',
            'PROPERTY_TYPE' => 'S',
            'SORT' => 220,
        ]);
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $iblockId = (int)\Bitrix\Main\Config\Option::get('main', self::OPTION_IBLOCK_ID);
        if ($iblockId > 0) {
            $helper->Iblock()->deleteIblock($iblockId);
        }

        \Bitrix\Main\Config\Option::delete('main', ['name' => self::OPTION_IBLOCK_ID]);
    }
}
