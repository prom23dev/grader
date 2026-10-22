<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

class VersionSeedTestProjects20261022140000 extends Version
{
    protected $author = "";

    protected $description = "Тестовые данные: 20 проектов иблока «Проекты» (PROJECTS_IBLOCK_ID) с разными статусами PR_STATUS (Ошибка/На проверке/Реализация/Завершён/Реализован), разной активностью (ACTIVE) и разным набором файлов (PR_PHOTO, для завершённых/реализованных — также PR_REPORT_PHOTOS и отчётные поля). Элементы помечены XML_ID (префикс test-project-), поэтому миграция идемпотентна и откатывается по XML_ID.";

    protected $moduleVersion = "5.15.0";

    const XML_ID_PREFIX = 'test-project-';

    // PR_STATUS enum ID => человекочитаемое имя (см. CODEX_MEMORY.md / project_donations_and_reports)
    const STATUS_ERROR = 6;       // Ошибка
    const STATUS_REVIEW = 7;      // На проверке
    const STATUS_IN_PROGRESS = 8; // Реализация
    const STATUS_DONE = 9;        // Завершён
    const STATUS_REPORTED = 10;   // Реализован (итоговый отчёт опубликован)

    public function up()
    {
        Loader::includeModule('iblock');

        $sample = $this->findSampleGeo();
        $photoDir = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/grader/assets/img/projects/';
        $photos = ['photo_1.png', 'photo_2.png', 'photo_3.png', 'photo_4.png', 'photo_5.png'];

        $items = [];
        for ($i = 1; $i <= 20; $i++) {
            $items[] = $this->buildItem($i, $sample, $photoDir, $photos);
        }

        $created = 0;
        $skipped = 0;
        foreach ($items as $item) {
            if ($this->findElementIdByXmlId(PROJECTS_IBLOCK_ID, $item['XML_ID'])) {
                $skipped++;
                continue;
            }

            $element = new \CIBlockElement();
            $elementId = $element->Add($item['FIELDS']);
            if (!$elementId) {
                throw new \Exception('Не удалось создать тестовый проект "' . $item['FIELDS']['NAME'] . '": ' . $element->LAST_ERROR);
            }
            $created++;
        }

        $this->outInfo("Создано тестовых проектов: {$created}, уже существовало: {$skipped}.");
    }

    public function down()
    {
        Loader::includeModule('iblock');

        $res = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => PROJECTS_IBLOCK_ID,
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

    private function buildItem(int $index, array $sample, string $photoDir, array $photos): array
    {
        $statusMap = [
            self::STATUS_ERROR,
            self::STATUS_REVIEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
            self::STATUS_REPORTED,
        ];
        $status = $statusMap[($index - 1) % count($statusMap)];
        $active = ($status === self::STATUS_ERROR || $index % 7 === 0) ? 'N' : 'Y';

        $statusNames = [
            self::STATUS_ERROR => 'Ошибка',
            self::STATUS_REVIEW => 'На проверке',
            self::STATUS_IN_PROGRESS => 'Реализация',
            self::STATUS_DONE => 'Завершён',
            self::STATUS_REPORTED => 'Реализован',
        ];
        $statusName = $statusNames[$status];

        $xmlId = self::XML_ID_PREFIX . $index;
        $name = "Тестовый проект №{$index} ({$statusName})";
        $address = $sample['settlementName']
            ? $sample['regionName'] . ', ' . $sample['districtName'] . ', ' . $sample['settlementName'] . ', ул. Тестовая, д. ' . $index
            : 'Тестовый адрес №' . $index;

        $photoCount = 1 + ($index % count($photos));
        $photoProps = [];
        for ($p = 0; $p < $photoCount; $p++) {
            $sourcePath = $photoDir . $photos[$p];
            if (file_exists($sourcePath)) {
                $fileArray = \CFile::MakeFileArray($sourcePath);
                if ($fileArray) {
                    $photoProps[] = $fileArray;
                }
            }
        }

        $props = [
            'PR_LOCATION' => $sample['region'],
            'PR_LOCATION_DISTRICT' => $sample['district'],
            'PR_LOCATION_SETTLEMENT' => $sample['settlement'],
            'PR_ADDRESS' => $address,
            'PR_WORK_TYPE_TEXT' => 'Благоустройство территории',
            'PR_STATUS' => $status,
            'PR_ADDITIONAL_INFO' => 'Автоматически созданный тестовый проект №' . $index . ' для проверки отображения статусов и файлов.',
            'PR_USER_NAME' => 'Тестовый Заявитель ' . $index,
            'PR_USER_PHONE' => '+7 900 000 00' . sprintf('%02d', $index),
            'PR_EMAIL' => 'test-project-' . $index . '@example.test',
            'PR_COST' => 10000 * $index,
        ];

        if ($photoProps) {
            $props['PR_PHOTO'] = $photoProps;
        }

        if ($status === self::STATUS_DONE || $status === self::STATUS_REPORTED) {
            $reportPhotoCount = 1 + (($index + 1) % count($photos));
            $reportPhotos = [];
            for ($p = 0; $p < $reportPhotoCount; $p++) {
                $sourcePath = $photoDir . $photos[$p];
                if (file_exists($sourcePath)) {
                    $fileArray = \CFile::MakeFileArray($sourcePath);
                    if ($fileArray) {
                        $reportPhotos[] = $fileArray;
                    }
                }
            }

            $props['PR_REPORT_TEXT'] = 'Итоговый отчёт по тестовому проекту №' . $index . ': работы выполнены, средства израсходованы по назначению.';
            $props['PR_REPORT_AMOUNT'] = (string)(9500 * $index);
            $props['PR_REPORT_DATE'] = (new \Bitrix\Main\Type\DateTime())->add('-' . $index . ' days')->toString();
            if ($reportPhotos) {
                $props['PR_REPORT_PHOTOS'] = $reportPhotos;
            }
        }

        return [
            'XML_ID' => $xmlId,
            'FIELDS' => [
                'IBLOCK_ID' => PROJECTS_IBLOCK_ID,
                'IBLOCK_SECTION_ID' => 1,
                'NAME' => $name,
                'CODE' => $xmlId,
                'XML_ID' => $xmlId,
                'ACTIVE' => $active,
                'PROPERTY_VALUES' => $props,
            ],
        ];
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

    private function findSampleGeo(): array
    {
        $empty = [
            'region' => '', 'regionName' => '',
            'district' => '', 'districtName' => '',
            'settlement' => '', 'settlementName' => '',
        ];

        Loader::includeModule('highloadblock');
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
