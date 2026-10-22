<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;

class VersionAddGeoXmlIdField20261012160000 extends Version
{
    protected $author = "";

    protected $description = "Добавляет поле UF_XML_ID (транслит UF_NAME) в HL-блоки GeoRegion/GeoDistrict/GeoSettlement, чтобы их можно было использовать как справочники по человекочитаемому коду, а не только по числовому ID элемента";

    protected $moduleVersion = "5.15.0";

    const HLBLOCKS = ['GeoRegion', 'GeoDistrict', 'GeoSettlement'];

    const BATCH_SIZE = 1000;

    /**
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $this->checkRequiredVersions(['VersionCreateGeoHlblocks20261007120000']);

        $helper = $this->getHelperManager();

        foreach (self::HLBLOCKS as $hlblockName) {
            $hlblockId = $helper->Hlblock()->getHlblockIdIfExists($hlblockName);

            $helper->Hlblock()->saveField($hlblockId, [
                'FIELD_NAME'        => 'UF_XML_ID',
                'USER_TYPE_ID'      => 'string',
                'XML_ID'            => 'UF_XML_ID',
                'SORT'              => 150,
                'MULTIPLE'          => 'N',
                'MANDATORY'         => 'N',
                'SHOW_IN_LIST'      => 'Y',
                'EDIT_IN_LIST'      => 'Y',
                'IS_SEARCHABLE'     => 'Y',
                'EDIT_FORM_LABEL'   => ['ru' => 'XML_ID (транслит названия)', 'en' => 'XML_ID (translit)'],
                'LIST_COLUMN_LABEL' => ['ru' => 'XML_ID', 'en' => 'XML_ID'],
                'LIST_FILTER_LABEL' => ['ru' => 'XML_ID', 'en' => 'XML_ID'],
            ]);

            $this->outNotice('Поле UF_XML_ID добавлено в ' . $hlblockName . ', заполняю транслитом...');
            $this->fillXmlId($helper->Hlblock()->getHlblockTableName($hlblockId));
            $this->outNotice('Заполнено для ' . $hlblockName);
        }
    }

    /**
     * @throws Exceptions\HelperException
     */
    public function down()
    {
        $helper = $this->getHelperManager();
        foreach (self::HLBLOCKS as $hlblockName) {
            $helper->Hlblock()->deleteField($hlblockName, 'UF_XML_ID');
        }
    }

    /**
     * Транслитерация UF_NAME -> UF_XML_ID требует настоящей посимвольной
     * транслитерации (CUtil::translit), её нельзя выразить в чистом SQL —
     * поэтому читаем все строки, считаем слаг в PHP, но пишем обратно
     * батчами через один UPDATE...CASE на батч, а не построчными UPDATE:
     * на GeoSettlement (~150k строк) построчные запросы были бы слишком
     * медленными (тот же приём батчинга, что и в geo_hlblock_migration для
     * batch-вставки населённых пунктов).
     *
     * Коллизии слагов (разные элементы транслитерируются в одинаковый
     * текст) разрешаются добавлением числового суффикса "-2", "-3"... в
     * порядке возрастания ID — в пределах одного HL-блока, без учёта
     * иерархии родителей.
     */
    private function fillXmlId(string $tableName): void
    {
        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $quotedTable = $sqlHelper->quote($tableName);

        $rows = $connection->query("SELECT ID, UF_NAME FROM {$quotedTable} ORDER BY ID")->fetchAll();

        $usedSlugs = [];
        $batch = [];

        $flush = function () use (&$batch, $connection, $sqlHelper, $quotedTable) {
            if (!$batch) {
                return;
            }

            $cases = [];
            foreach ($batch as $id => $slug) {
                $cases[] = 'WHEN ' . (int)$id . " THEN '" . $sqlHelper->forSql($slug) . "'";
            }

            $sql = "UPDATE {$quotedTable} SET UF_XML_ID = CASE ID "
                . implode(' ', $cases)
                . ' END WHERE ID IN (' . implode(',', array_map('intval', array_keys($batch))) . ')';

            $connection->queryExecute($sql);
            $batch = [];
        };

        foreach ($rows as $row) {
            $slug = \CUtil::translit((string)$row['UF_NAME'], 'ru', [
                'max_len'       => 100,
                'change_case'   => 'L',
                'replace_space' => '-',
                'replace_other' => '-',
            ]);
            $slug = trim($slug, '-');
            if ($slug === '') {
                $slug = 'item-' . $row['ID'];
            }

            // Пробуем "-2", "-3", ... пока не найдём значение, которое не
            // занято ни другим дублем, ни уже встретившимся названием,
            // естественно транслитерирующимся в такой же вид (например,
            // реальный "Афанасово-3" мог бы совпасть с третьим дублем
            // "Афанасово").
            $candidate = $slug;
            $suffix = 1;
            while (isset($usedSlugs[$candidate])) {
                $suffix++;
                $candidate = $slug . '-' . $suffix;
            }
            $usedSlugs[$candidate] = true;

            $batch[(int)$row['ID']] = $candidate;
            if (count($batch) >= self::BATCH_SIZE) {
                $flush();
            }
        }

        $flush();
    }
}
