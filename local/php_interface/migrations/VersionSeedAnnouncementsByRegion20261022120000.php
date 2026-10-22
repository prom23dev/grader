<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class VersionSeedAnnouncementsByRegion20261022120000 extends Version
{
    protected $author = "";

    protected $description = "Объявления шапки, привязанные к нескольким разным регионам (HL-блок GeoRegion) — каждое ведёт на свою детальную страницу в разделе /announcements/. Элементы помечены XML_ID, миграция идемпотентна и откатывается по XML_ID.";

    protected $moduleVersion = "5.15.0";

    const XML_ID_PREFIX = 'announce-region-';

    const REGIONS_LIMIT = 6;

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

        $regions = $this->findRegions(self::REGIONS_LIMIT);
        if (!$regions) {
            $this->outInfo('В HL-блоке GeoRegion нет данных — объявления по регионам не созданы.');
            return;
        }

        $now = new \Bitrix\Main\Type\DateTime();

        $created = 0;
        foreach ($regions as $region) {
            $xmlId = self::XML_ID_PREFIX . $region['ID'];
            if ($this->findElementIdByXmlId($iblockId, $xmlId)) {
                continue;
            }

            $fields = [
                'IBLOCK_ID' => $iblockId,
                'NAME' => 'Для региона «' . $region['NAME'] . '»: новости и анонсы проектов',
                'XML_ID' => $xmlId,
                'ACTIVE' => 'Y',
                'ACTIVE_FROM' => $now,
                'PROPERTY_VALUES' => [
                    'ANNOUNCE_LINK' => '',
                    'PR_ANNOUNCE_REGION' => (string)$region['ID'],
                    'PR_ANNOUNCE_DISTRICT' => '',
                    'PR_ANNOUNCE_SETTLEMENT' => '',
                ],
            ];

            $element = new \CIBlockElement();
            $elementId = $element->Add($fields);
            if (!$elementId) {
                throw new \Exception('Не удалось создать объявление для региона "' . $region['NAME'] . '": ' . $element->LAST_ERROR);
            }
            $created++;
        }

        $this->outInfo("Создано объявлений по регионам: {$created} из " . count($regions) . ". Каждое открывается на своей странице в /announcements/{ID}/ и видно только жителям соответствующего региона (либо при ?all=Y на /announcements/).");
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
     * @return array<int, array{ID: int, NAME: string}>
     */
    private function findRegions(int $limit): array
    {
        $regionTable = $this->getHlTableName('GeoRegion');
        if (!$regionTable) {
            return [];
        }

        $connection = Application::getConnection();
        $rows = $connection->query("
            SELECT ID, UF_NAME
            FROM {$regionTable}
            ORDER BY ID ASC
            LIMIT {$limit}
        ")->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = ['ID' => (int)$row['ID'], 'NAME' => (string)$row['UF_NAME']];
        }

        return $result;
    }

    private function getHlTableName(string $hlblockName): ?string
    {
        $hlblock = HighloadBlockTable::getRow(['filter' => ['NAME' => $hlblockName]]);
        return $hlblock['TABLE_NAME'] ?? null;
    }
}
