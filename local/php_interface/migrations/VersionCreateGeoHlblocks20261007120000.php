<?php

namespace Sprint\Migration;

class VersionCreateGeoHlblocks20261007120000 extends Version
{
    protected $author = "";

    protected $description = "Создаёт HL-блоки географической иерархии: регион -> район -> населённый пункт";

    protected $moduleVersion = "5.15.0";

    /**
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $regionId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'GeoRegion',
            'TABLE_NAME' => 'grader_region',
            'LANG'       => [
                'ru' => ['NAME' => 'Гео: регионы'],
            ],
        ]);

        $helper->Hlblock()->saveField($regionId, [
            'FIELD_NAME'        => 'UF_NAME',
            'USER_TYPE_ID'      => 'string',
            'XML_ID'            => 'UF_NAME',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'Y',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Название', 'en' => 'Name'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Название', 'en' => 'Name'],
            'LIST_FILTER_LABEL' => ['ru' => 'Название', 'en' => 'Name'],
        ]);

        $districtId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'GeoDistrict',
            'TABLE_NAME' => 'grader_district',
            'LANG'       => [
                'ru' => ['NAME' => 'Гео: районы'],
            ],
        ]);

        $helper->Hlblock()->saveField($districtId, [
            'FIELD_NAME'        => 'UF_NAME',
            'USER_TYPE_ID'      => 'string',
            'XML_ID'            => 'UF_NAME',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'Y',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Название', 'en' => 'Name'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Название', 'en' => 'Name'],
            'LIST_FILTER_LABEL' => ['ru' => 'Название', 'en' => 'Name'],
        ]);

        $helper->Hlblock()->saveField($districtId, [
            'FIELD_NAME'        => 'UF_REGION',
            'USER_TYPE_ID'      => 'hlblock',
            'XML_ID'            => 'UF_REGION',
            'SORT'              => 200,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'Y',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'N',
            'SETTINGS'          => ['HLBLOCK_ID' => 'GeoRegion'],
            'EDIT_FORM_LABEL'   => ['ru' => 'Регион', 'en' => 'Region'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Регион', 'en' => 'Region'],
            'LIST_FILTER_LABEL' => ['ru' => 'Регион', 'en' => 'Region'],
        ]);

        $settlementId = $helper->Hlblock()->saveHlblock([
            'NAME'       => 'GeoSettlement',
            'TABLE_NAME' => 'grader_settlement',
            'LANG'       => [
                'ru' => ['NAME' => 'Гео: населённые пункты'],
            ],
        ]);

        $helper->Hlblock()->saveField($settlementId, [
            'FIELD_NAME'        => 'UF_NAME',
            'USER_TYPE_ID'      => 'string',
            'XML_ID'            => 'UF_NAME',
            'SORT'              => 100,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'Y',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'Y',
            'EDIT_FORM_LABEL'   => ['ru' => 'Название', 'en' => 'Name'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Название', 'en' => 'Name'],
            'LIST_FILTER_LABEL' => ['ru' => 'Название', 'en' => 'Name'],
        ]);

        $helper->Hlblock()->saveField($settlementId, [
            'FIELD_NAME'        => 'UF_DISTRICT',
            'USER_TYPE_ID'      => 'hlblock',
            'XML_ID'            => 'UF_DISTRICT',
            'SORT'              => 200,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'Y',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'N',
            'SETTINGS'          => ['HLBLOCK_ID' => 'GeoDistrict'],
            'EDIT_FORM_LABEL'   => ['ru' => 'Район', 'en' => 'District'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Район', 'en' => 'District'],
            'LIST_FILTER_LABEL' => ['ru' => 'Район', 'en' => 'District'],
        ]);
    }

    /**
     * @throws Exceptions\HelperException
     */
    public function down()
    {
        $helper = $this->getHelperManager();

        $helper->Hlblock()->deleteHlblockIfExists('GeoSettlement');
        $helper->Hlblock()->deleteHlblockIfExists('GeoDistrict');
        $helper->Hlblock()->deleteHlblockIfExists('GeoRegion');
    }
}
