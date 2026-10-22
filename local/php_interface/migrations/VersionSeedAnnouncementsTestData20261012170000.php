<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class VersionSeedAnnouncementsTestData20261012170000 extends Version
{
    protected $author = "";

    protected $description = "Тестовые данные для иблока «Объявления шапки» / страницы /announcements/: несколько объявлений без гео-привязки (видны всем) и несколько с привязкой к реальным региону/району/населённому пункту из HL-блоков GeoRegion/GeoDistrict/GeoSettlement (видны только жителям соответствующей гео-зоны либо при ?all=Y). Элементы помечены XML_ID, поэтому миграция идемпотентна и откатывается по XML_ID, а не по ID.";

    protected $moduleVersion = "5.15.0";

    const XML_ID_PREFIX = 'announce-test-';

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

        $sample = $this->findSampleGeo();

        $now = new \Bitrix\Main\Type\DateTime();
        $yesterday = (new \Bitrix\Main\Type\DateTime())->add('-1 days');

        $items = [
            [
                'XML_ID' => self::XML_ID_PREFIX . 'all-1',
                'NAME' => 'Портал обновил правила модерации объявлений',
                'LINK' => '',
                'REGION' => '', 'DISTRICT' => '', 'SETTLEMENT' => '',
            ],
            [
                'XML_ID' => self::XML_ID_PREFIX . 'all-2',
                'NAME' => 'Напоминаем: отчёты по завершённым проектам нужно публиковать в течение 5 дней',
                'LINK' => '/about/#faq',
                'REGION' => '', 'DISTRICT' => '', 'SETTLEMENT' => '',
            ],
            [
                'XML_ID' => self::XML_ID_PREFIX . 'archived',
                'NAME' => 'Архивное объявление (не должно отображаться — ACTIVE_TO в прошлом)',
                'LINK' => '',
                'REGION' => '', 'DISTRICT' => '', 'SETTLEMENT' => '',
                'ACTIVE_TO' => $yesterday,
            ],
        ];

        if ($sample['region']) {
            $items[] = [
                'XML_ID' => self::XML_ID_PREFIX . 'region',
                'NAME' => 'Для жителей региона «' . $sample['regionName'] . '»: изменился график работы поддержки',
                'LINK' => '',
                'REGION' => $sample['region'], 'DISTRICT' => '', 'SETTLEMENT' => '',
            ];
        }

        if ($sample['district']) {
            $items[] = [
                'XML_ID' => self::XML_ID_PREFIX . 'district',
                'NAME' => 'Для жителей района «' . $sample['districtName'] . '»: набор волонтёров на весну',
                'LINK' => '',
                'REGION' => $sample['region'], 'DISTRICT' => $sample['district'], 'SETTLEMENT' => '',
            ];
        }

        if ($sample['settlement']) {
            $items[] = [
                'XML_ID' => self::XML_ID_PREFIX . 'settlement',
                'NAME' => 'Только для «' . $sample['settlementName'] . '»: встреча жителей по проекту благоустройства',
                'LINK' => '/about/',
                'REGION' => $sample['region'], 'DISTRICT' => $sample['district'], 'SETTLEMENT' => $sample['settlement'],
            ];
        }

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
                'ACTIVE_TO' => $item['ACTIVE_TO'] ?? '',
                'PROPERTY_VALUES' => [
                    'ANNOUNCE_LINK' => $item['LINK'],
                    'PR_ANNOUNCE_REGION' => $item['REGION'],
                    'PR_ANNOUNCE_DISTRICT' => $item['DISTRICT'],
                    'PR_ANNOUNCE_SETTLEMENT' => $item['SETTLEMENT'],
                ],
            ];

            $element = new \CIBlockElement();
            $elementId = $element->Add($fields);
            if (!$elementId) {
                throw new \Exception('Не удалось создать тестовое объявление "' . $item['NAME'] . '": ' . $element->LAST_ERROR);
            }
            $created++;
        }

        $hint = $sample['settlementName']
            ? " Чтобы увидеть гео-привязанные объявления, выберите на карте город «{$sample['settlementName']}» (регион «{$sample['regionName']}», район «{$sample['districtName']}»)."
            : ' Гео-привязанные тестовые объявления не созданы — в HL-блоках GeoRegion/GeoDistrict/GeoSettlement нет данных.';
        $this->outInfo("Создано тестовых объявлений: {$created}.{$hint}");
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
     * Берёт первые попавшиеся связанные регион/район/нас. пункт из HL-блоков
     * GeoRegion/GeoDistrict/GeoSettlement, чтобы тестовые объявления были
     * привязаны к реально существующей гео-цепочке, а не к произвольным ID.
     */
    private function findSampleGeo(): array
    {
        $empty = [
            'region' => '', 'regionName' => '',
            'district' => '', 'districtName' => '',
            'settlement' => '', 'settlementName' => '',
        ];

        $connection = Application::getConnection();

        $regionTable = $this->getHlTableName('GeoRegion');
        $districtTable = $this->getHlTableName('GeoDistrict');
        $settlementTable = $this->getHlTableName('GeoSettlement');
        if (!$regionTable || !$districtTable || !$settlementTable) {
            return $empty;
        }

        $row = $connection->query("
            SELECT
                r.ID AS REGION_ID, r.UF_NAME AS REGION_NAME,
                d.ID AS DISTRICT_ID, d.UF_NAME AS DISTRICT_NAME,
                s.ID AS SETTLEMENT_ID, s.UF_NAME AS SETTLEMENT_NAME
            FROM {$settlementTable} s
            INNER JOIN {$districtTable} d ON d.ID = s.UF_DISTRICT
            INNER JOIN {$regionTable} r ON r.ID = d.UF_REGION
            ORDER BY s.ID ASC
            LIMIT 1
        ")->fetch();

        if (!$row) {
            return $empty;
        }

        return [
            'region' => (string)$row['REGION_ID'], 'regionName' => (string)$row['REGION_NAME'],
            'district' => (string)$row['DISTRICT_ID'], 'districtName' => (string)$row['DISTRICT_NAME'],
            'settlement' => (string)$row['SETTLEMENT_ID'], 'settlementName' => (string)$row['SETTLEMENT_NAME'],
        ];
    }

    private function getHlTableName(string $hlblockName): ?string
    {
        $hlblock = HighloadBlockTable::getRow(['filter' => ['NAME' => $hlblockName]]);
        return $hlblock['TABLE_NAME'] ?? null;
    }
}
