<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class VersionSeedAnnouncementsKrasnodar20261022130000 extends Version
{
    protected $author = "";

    protected $description = "Пара объявлений шапки для Краснодара (регион «Краснодарский край», населённый пункт «Краснодар») — видны жителям Краснодара либо при ?all=Y на /announcements/. Элементы помечены XML_ID, миграция идемпотентна.";

    protected $moduleVersion = "5.15.0";

    const XML_ID_PREFIX = 'announce-krasnodar-';

    /**
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $this->checkRequiredVersions(['VersionCreateHeaderAnnounceIblock20261012120000']);

        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');

        $iblockId = (int)Option::get('main', 'grader_header_announce_iblock_id');
        if ($iblockId <= 0) {
            throw new \Exception('Иблок «Объявления шапки» не найден (grader_header_announce_iblock_id)');
        }

        $geo = $this->findKrasnodar();
        if (!$geo) {
            $this->outInfo('Населённый пункт «Краснодар» не найден в HL-блоке GeoSettlement — объявления не созданы.');
            return;
        }

        $now = new \Bitrix\Main\Type\DateTime();

        $items = [
            [
                'XML_ID' => self::XML_ID_PREFIX . '1',
                'NAME' => 'Краснодар: открыт приём заявок на новые проекты благоустройства',
            ],
            [
                'XML_ID' => self::XML_ID_PREFIX . '2',
                'NAME' => 'Краснодар: встреча с жителями по итогам завершённых проектов',
            ],
        ];

        $created = 0;
        foreach ($items as $item) {
            if ($this->findElementIdByXmlId($iblockId, $item['XML_ID'])) {
                continue;
            }

            $fields = [
                'IBLOCK_ID' => $iblockId,
                'NAME' => $item['NAME'],
                'XML_ID' => $item['XML_ID'],
                'ACTIVE' => 'Y',
                'ACTIVE_FROM' => $now,
                'PROPERTY_VALUES' => [
                    'ANNOUNCE_LINK' => '',
                    'PR_ANNOUNCE_REGION' => (string)$geo['region'],
                    'PR_ANNOUNCE_DISTRICT' => (string)$geo['district'],
                    'PR_ANNOUNCE_SETTLEMENT' => (string)$geo['settlement'],
                ],
            ];

            $element = new \CIBlockElement();
            $elementId = $element->Add($fields);
            if (!$elementId) {
                throw new \Exception('Не удалось создать объявление "' . $item['NAME'] . '": ' . $element->LAST_ERROR);
            }
            $created++;
        }

        $this->outInfo("Создано объявлений для Краснодара: {$created}.");
    }

    public function down()
    {
        Loader::includeModule('iblock');

        $iblockId = (int)Option::get('main', 'grader_header_announce_iblock_id');
        if ($iblockId <= 0) {
            return;
        }

        $res = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $iblockId,
                '%XML_ID' => self::XML_ID_PREFIX,
            ],
            false,
            false,
            ['ID']
        );
        while ($row = $res->Fetch()) {
            \CIBlockElement::Delete((int)$row['ID']);
        }
    }

    private function findElementIdByXmlId(int $iblockId, string $xmlId): int
    {
        $row = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $iblockId, 'XML_ID' => $xmlId],
            false,
            false,
            ['ID']
        )->Fetch();

        return $row ? (int)$row['ID'] : 0;
    }

    /**
     * @return array{region: int, district: int, settlement: int}|null
     */
    private function findKrasnodar(): ?array
    {
        $settlementTable = $this->getHlTableName('GeoSettlement');
        $districtTable = $this->getHlTableName('GeoDistrict');
        $regionTable = $this->getHlTableName('GeoRegion');
        if (!$settlementTable || !$districtTable || !$regionTable) {
            return null;
        }

        $connection = Application::getConnection();
        $row = $connection->query("
            SELECT s.ID AS SETTLEMENT_ID, d.ID AS DISTRICT_ID, r.ID AS REGION_ID
            FROM {$settlementTable} s
            INNER JOIN {$districtTable} d ON d.ID = s.UF_DISTRICT
            INNER JOIN {$regionTable} r ON r.ID = d.UF_REGION
            WHERE s.UF_NAME = 'Краснодар' AND r.UF_NAME = 'Краснодарский край'
            LIMIT 1
        ")->fetch();

        if (!$row) {
            return null;
        }

        return [
            'region' => (int)$row['REGION_ID'],
            'district' => (int)$row['DISTRICT_ID'],
            'settlement' => (int)$row['SETTLEMENT_ID'],
        ];
    }

    private function getHlTableName(string $hlblockName): ?string
    {
        $hlblock = HighloadBlockTable::getRow(['filter' => ['NAME' => $hlblockName]]);
        return $hlblock['TABLE_NAME'] ?? null;
    }
}
