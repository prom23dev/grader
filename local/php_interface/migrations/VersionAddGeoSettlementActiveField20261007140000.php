<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;

class VersionAddGeoSettlementActiveField20261007140000 extends Version
{
    protected $author = "";

    protected $description = "Добавляет поле UF_ACTIVE в HL-блок GeoSettlement для модерации пользовательских населённых пунктов";

    protected $moduleVersion = "5.15.0";

    /**
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $this->checkRequiredVersions(['VersionCreateGeoHlblocks20261007120000']);

        $helper = $this->getHelperManager();

        $settlementId = $helper->Hlblock()->getHlblockIdIfExists('GeoSettlement');

        $helper->Hlblock()->saveField($settlementId, [
            'FIELD_NAME'        => 'UF_ACTIVE',
            'USER_TYPE_ID'      => 'boolean',
            'XML_ID'            => 'UF_ACTIVE',
            'SORT'              => 300,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'N',
            'SETTINGS'          => ['DEFAULT_VALUE' => 1],
            'EDIT_FORM_LABEL'   => ['ru' => 'Активен (прошёл модерацию)', 'en' => 'Active (moderated)'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Активен', 'en' => 'Active'],
            'LIST_FILTER_LABEL' => ['ru' => 'Активен', 'en' => 'Active'],
        ]);

        $tableName = $helper->Hlblock()->getHlblockTableName($settlementId);
        $connection = Application::getConnection();
        $connection->queryExecute(
            'UPDATE ' . $connection->getSqlHelper()->quote($tableName) . ' SET UF_ACTIVE = 1 WHERE UF_ACTIVE IS NULL'
        );
    }

    /**
     * @throws Exceptions\HelperException
     */
    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteField('GeoSettlement', 'UF_ACTIVE');
    }
}
