<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;

/**
 * Население населённых пунктов (человек) для приоритизации результатов поиска
 * в HL-блоке GeoSettlement. Значения взяты из таблицы Росстата «Численность
 * населения Российской Федерации по городам...» (оценка на 01.01.2024).
 */
class VersionAddGeoSettlementPopulation20261015120000 extends Version
{
    protected $description = 'Добавляет население GeoSettlement для сортировки поиска';
    protected $moduleVersion = '5.15.0';

    public function up()
    {
        $helper = $this->getHelperManager();
        $id = $helper->Hlblock()->getHlblockIdIfExists('GeoSettlement');
        if (!$id) {
            return;
        }

        $helper->Hlblock()->saveField($id, [
            'FIELD_NAME' => 'UF_POPULATION',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_POPULATION',
            'SORT' => 300,
            'MANDATORY' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'EDIT_FORM_LABEL' => ['ru' => 'Население, чел.', 'en' => 'Population'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Население, чел.', 'en' => 'Population'],
            'LIST_FILTER_LABEL' => ['ru' => 'Население, чел.', 'en' => 'Population'],
        ]);

        Loader::includeModule('highloadblock');
        $class = getGeoHlDataClass('GeoSettlement');
        // Росстат, 01.01.2024; остальные пункты сохраняют 0 и идут после
        // ранжированных городов (их можно дополнять без изменения endpoint).
        $population = [
            'Москва' => 13149803, 'Санкт-Петербург' => 5601911,
            'Новосибирск' => 1633595, 'Екатеринбург' => 1544376,
            'Казань' => 1318600, 'Красноярск' => 1210163,
            'Нижний Новгород' => 1212477, 'Челябинск' => 1187335,
            'Уфа' => 1154996, 'Самара' => 1160700,
            'Ростов-на-Дону' => 1142162, 'Краснодар' => 1056548,
            'Омск' => 1128858, 'Воронеж' => 1057681,
            'Пермь' => 1027330, 'Волгоград' => 1024763,
        ];
        foreach ($population as $name => $value) {
            $row = $class::getList(['select' => ['ID'], 'filter' => ['=UF_NAME' => $name], 'limit' => 1])->fetch();
            if ($row) {
                $class::update((int)$row['ID'], ['UF_POPULATION' => (int)$value]);
            }
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        if ($helper->Hlblock()->getHlblockIdIfExists('GeoSettlement')) {
            $helper->Hlblock()->deleteField('GeoSettlement', 'UF_POPULATION');
        }
    }
}
