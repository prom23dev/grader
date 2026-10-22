<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;

class VersionMigrateProjectLocationToHl20261008150000 extends Version
{
    protected $author = "";

    protected $description = "Переводит текстовые значения PR_LOCATION/PR_LOCATION_DISTRICT/PR_LOCATION_SETTLEMENT существующих проектов на ID элементов HL-блоков GeoRegion/GeoDistrict/GeoSettlement. Делается одним bulk UPDATE с JOIN на каждый уровень (~150k строк проектов — построчный CIBlockElement API здесь непригоден по скорости). Несовпадения логируются в project_location_migration_mismatches.log для ручной сверки и остаются текстом (getGeoName() отдаёт их как есть).";

    protected $moduleVersion = "5.15.0";

    private function getHlTableName(string $hlblockName): string
    {
        $hlblock = HighloadBlockTable::getRow(['filter' => ['NAME' => $hlblockName]]);
        return $hlblock['TABLE_NAME'];
    }

    /**
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $this->checkRequiredVersions(['VersionCreateGeoHlblocks20261007120000']);

        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $connection = Application::getConnection();

        $regionTable = $this->getHlTableName('GeoRegion');
        $districtTable = $this->getHlTableName('GeoDistrict');
        $settlementTable = $this->getHlTableName('GeoSettlement');

        $regionPropId = (int)\CIBlockProperty::GetList([], ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'CODE' => 'PR_LOCATION'])->Fetch()['ID'];
        $districtPropId = (int)\CIBlockProperty::GetList([], ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'CODE' => 'PR_LOCATION_DISTRICT'])->Fetch()['ID'];
        $settlementPropId = (int)\CIBlockProperty::GetList([], ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, 'CODE' => 'PR_LOCATION_SETTLEMENT'])->Fetch()['ID'];

        // 1. Регион: прямое совпадение по имени, без привязки к родителю.
        $connection->queryExecute("
            UPDATE b_iblock_element_property p
            INNER JOIN {$regionTable} r ON r.UF_NAME = p.VALUE
            SET p.VALUE = r.ID
            WHERE p.IBLOCK_PROPERTY_ID = {$regionPropId}
              AND p.VALUE NOT REGEXP '^[0-9]+$'
        ");

        // 2. Район: совпадение по имени И по уже мигрированному региону-родителю
        // (защита от одноимённых районов в разных регионах).
        $connection->queryExecute("
            UPDATE b_iblock_element_property pd
            INNER JOIN b_iblock_element_property pr
                ON pr.IBLOCK_ELEMENT_ID = pd.IBLOCK_ELEMENT_ID AND pr.IBLOCK_PROPERTY_ID = {$regionPropId}
            INNER JOIN {$districtTable} d
                ON d.UF_NAME = pd.VALUE AND d.UF_REGION = pr.VALUE
            SET pd.VALUE = d.ID
            WHERE pd.IBLOCK_PROPERTY_ID = {$districtPropId}
              AND pd.VALUE NOT REGEXP '^[0-9]+$'
              AND pr.VALUE REGEXP '^[0-9]+$'
        ");

        // 3. Населённый пункт: совпадение по имени И по уже мигрированному району-родителю.
        $connection->queryExecute("
            UPDATE b_iblock_element_property ps
            INNER JOIN b_iblock_element_property pd2
                ON pd2.IBLOCK_ELEMENT_ID = ps.IBLOCK_ELEMENT_ID AND pd2.IBLOCK_PROPERTY_ID = {$districtPropId}
            INNER JOIN {$settlementTable} s
                ON s.UF_NAME = ps.VALUE AND s.UF_DISTRICT = pd2.VALUE
            SET ps.VALUE = s.ID
            WHERE ps.IBLOCK_PROPERTY_ID = {$settlementPropId}
              AND ps.VALUE NOT REGEXP '^[0-9]+$'
              AND pd2.VALUE REGEXP '^[0-9]+$'
        ");

        // Несовпадения — агрегированы по уникальному значению, не по проекту
        // (при 150k+ строк построчный лог был бы бесполезен).
        $mismatchLines = [];
        $mismatchQueries = [
            'регион (GeoRegion)' => "SELECT VALUE, COUNT(*) c FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID = {$regionPropId} AND VALUE NOT REGEXP '^[0-9]+$' AND VALUE != '' GROUP BY VALUE ORDER BY c DESC",
            'район (GeoDistrict, в пределах своего региона)' => "SELECT VALUE, COUNT(*) c FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID = {$districtPropId} AND VALUE NOT REGEXP '^[0-9]+$' AND VALUE != '' GROUP BY VALUE ORDER BY c DESC",
            'нас. пункт (GeoSettlement, в пределах своего района)' => "SELECT VALUE, COUNT(*) c FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID = {$settlementPropId} AND VALUE NOT REGEXP '^[0-9]+$' AND VALUE != '' GROUP BY VALUE ORDER BY c DESC",
        ];
        $totalMismatches = 0;
        foreach ($mismatchQueries as $label => $sql) {
            $rows = $connection->query($sql)->fetchAll();
            if ($rows) {
                $mismatchLines[] = "Не сопоставлено для уровня «$label» (" . count($rows) . " уникальных значений):";
                foreach ($rows as $row) {
                    $mismatchLines[] = "  «{$row['VALUE']}» — {$row['c']} проект(ов)";
                    $totalMismatches += (int)$row['c'];
                }
            }
        }

        $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/migrations/project_location_migration_mismatches.log';
        if ($mismatchLines) {
            file_put_contents($logPath, implode("\n", $mismatchLines) . "\n");
        } elseif (file_exists($logPath)) {
            unlink($logPath);
        }

        $this->outInfo("Миграция PR_LOCATION* завершена. Несовпавших значений (на всех уровнях суммарно): $totalMismatches" . ($mismatchLines ? " — см. $logPath" : ''));
    }

    public function down()
    {
        // Откат текстом невозможен — исходные строки не хранятся отдельно от
        // уже перезаписанного числового значения. При необходимости
        // восстановить PR_LOCATION* из бэкапа БД, сделанного перед миграцией.
    }
}
