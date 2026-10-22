<?php

namespace Sprint\Migration;

/**
 * Добавляет файл герба населённого пункта в географический справочник.
 *
 * Герб хранится у GeoSettlement, а не у проекта: один населённый пункт может
 * использоваться в нескольких проектах и должен иметь единый источник герба.
 */
class VersionAddGeoSettlementCoatOfArms20261021110000 extends Version
{
    protected $description = 'Добавляет файл герба в HL-блок GeoSettlement';
    protected $moduleVersion = '5.15.0';

    public function up()
    {
        $this->checkRequiredVersions(['VersionCreateGeoHlblocks20261007120000']);

        $helper = $this->getHelperManager();
        $settlementId = $helper->Hlblock()->getHlblockIdIfExists('GeoSettlement');
        if (!$settlementId) {
            return;
        }

        $helper->Hlblock()->saveField($settlementId, [
            'FIELD_NAME'        => 'UF_COAT_OF_ARMS',
            'USER_TYPE_ID'      => 'file',
            'XML_ID'            => 'UF_COAT_OF_ARMS',
            'SORT'              => 400,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'N',
            'EDIT_FORM_LABEL'   => ['ru' => 'Герб населённого пункта', 'en' => 'Settlement coat of arms'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Герб', 'en' => 'Coat of arms'],
            'LIST_FILTER_LABEL' => ['ru' => 'Герб', 'en' => 'Coat of arms'],
        ]);
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        if ($helper->Hlblock()->getHlblockIdIfExists('GeoSettlement')) {
            $helper->Hlblock()->deleteField('GeoSettlement', 'UF_COAT_OF_ARMS');
        }
    }
}
