<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;

class VersionFillGeoHlblocksFromJson20261007120100 extends Version
{
    private const JSON_PATH = '/local/templates/grader/assets/json/geoData.json';
    private const BATCH_SIZE = 1000;

    protected $author = "";

    protected $description = "Заполняет HL-блоки региона/района/населённого пункта данными из geoData.json";

    protected $moduleVersion = "5.15.0";

    /**
     * @throws Exceptions\HelperException
     * @throws Exceptions\MigrationException
     */
    public function up()
    {
        $this->checkRequiredVersions(['VersionCreateGeoHlblocks20261007120000']);

        $helper = $this->getHelperManager();
        $geoData = $this->loadGeoData();

        $connection = Application::getConnection();
        $settlementTable = $helper->Hlblock()->getHlblockTableName('GeoSettlement');

        $settlementBuffer = [];

        foreach ($geoData['region'] as $regionName => $districts) {
            $regionId = $helper->Hlblock()->addElement('GeoRegion', [
                'UF_NAME' => $regionName,
            ]);

            foreach ($districts as $district) {
                $districtId = $helper->Hlblock()->addElement('GeoDistrict', [
                    'UF_NAME'   => $district['district'],
                    'UF_REGION' => $regionId,
                ]);

                foreach ($district['settlement'] as $settlementName) {
                    $settlementBuffer[] = [$settlementName, $districtId];

                    if (count($settlementBuffer) >= self::BATCH_SIZE) {
                        $this->flushSettlements($connection, $settlementTable, $settlementBuffer);
                        $settlementBuffer = [];
                    }
                }
            }

            $this->outNotice('Регион обработан: ' . $regionName);
        }

        $this->flushSettlements($connection, $settlementTable, $settlementBuffer);
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $connection = Application::getConnection();

        $connection->truncateTable($helper->Hlblock()->getHlblockTableName('GeoSettlement'));
        $connection->truncateTable($helper->Hlblock()->getHlblockTableName('GeoDistrict'));
        $connection->truncateTable($helper->Hlblock()->getHlblockTableName('GeoRegion'));
    }

    private function loadGeoData(): array
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . self::JSON_PATH;

        $json = file_get_contents($path);

        return json_decode($json, true);
    }

    private function flushSettlements(Connection $connection, string $table, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $sqlHelper = $connection->getSqlHelper();

        $values = [];
        foreach ($rows as [$name, $districtId]) {
            $values[] = '(\'' . $sqlHelper->forSql($name) . '\', ' . (int)$districtId . ')';
        }

        $sql = 'INSERT INTO ' . $sqlHelper->quote($table) . ' (UF_NAME, UF_DISTRICT) VALUES ' . implode(', ', $values);

        $connection->queryExecute($sql);
    }
}
