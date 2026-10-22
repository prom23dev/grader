<?php

namespace Sprint\Migration;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

class VersionSeedSaleForDonations20261008120000 extends Version
{
    protected $author = "";

    protected $description = "Заводит минимальную конфигурацию модуля sale (тип плательщика, свойства заказа, включение ПС Т-Банк) для оформления поддержки проекта через стандартный Order/Payment вместо прямого вызова Tinkoff API";

    protected $moduleVersion = "5.15.0";

    const OPTION_MODULE_ID = 'main';
    const OPTION_PERSON_TYPE_ID = 'grader_donation_person_type_id';
    const OPTION_PROPS_GROUP_ID = 'grader_donation_props_group_id';

    public function up()
    {
        Loader::includeModule('sale');

        $siteId = 's1';

        // Тип плательщика "Физическое лицо" для пожертвований на проекты
        $personTypeId = (int)Option::get(self::OPTION_MODULE_ID, self::OPTION_PERSON_TYPE_ID);
        if ($personTypeId <= 0) {
            $personTypeId = (int)\CSalePersonType::Add([
                'LID' => [$siteId],
                'NAME' => 'Физическое лицо (поддержка проекта)',
                'CODE' => 'GRADER_DONOR',
                'SORT' => 100,
                'ACTIVE' => 'Y',
            ]);
            if ($personTypeId <= 0) {
                throw new \Exception('Не удалось создать тип плательщика для пожертвований');
            }
            Option::set(self::OPTION_MODULE_ID, self::OPTION_PERSON_TYPE_ID, $personTypeId);
        }

        $propsGroupId = (int)Option::get(self::OPTION_MODULE_ID, self::OPTION_PROPS_GROUP_ID);
        if ($propsGroupId <= 0) {
            $propsGroupId = (int)\CSaleOrderPropsGroup::Add([
                'PERSON_TYPE_ID' => $personTypeId,
                'NAME' => 'Контактные данные',
                'SORT' => 100,
            ]);
            if ($propsGroupId <= 0) {
                throw new \Exception('Не удалось создать группу свойств заказа');
            }
            Option::set(self::OPTION_MODULE_ID, self::OPTION_PROPS_GROUP_ID, $propsGroupId);
        }

        $this->addPropIfNotExists($personTypeId, $propsGroupId, [
            'NAME' => 'E-Mail',
            'CODE' => 'EMAIL',
            'TYPE' => 'STRING',
            'REQUIRED' => 'N',
            'IS_EMAIL' => 'Y',
            'SORT' => 100,
        ]);

        $this->addPropIfNotExists($personTypeId, $propsGroupId, [
            'NAME' => 'Телефон',
            'CODE' => 'PHONE',
            'TYPE' => 'STRING',
            'REQUIRED' => 'N',
            'IS_PHONE' => 'Y',
            'SORT' => 200,
        ]);

        $this->addPropIfNotExists($personTypeId, $propsGroupId, [
            'NAME' => 'ФИО плательщика',
            'CODE' => 'FIO',
            'TYPE' => 'STRING',
            'REQUIRED' => 'N',
            'IS_PAYER' => 'Y',
            'IS_PROFILE_NAME' => 'Y',
            'SORT' => 300,
        ]);

        // Служебное свойство — id проекта, к которому относится платёж.
        // UTIL = Y скрывает его от стандартных форм оформления заказа.
        $this->addPropIfNotExists($personTypeId, $propsGroupId, [
            'NAME' => 'ID проекта',
            'CODE' => 'PROJECT_ID',
            'TYPE' => 'STRING',
            'REQUIRED' => 'N',
            'UTIL' => 'Y',
            'USER_PROPS' => 'N',
            'SORT' => 900,
        ]);

        // Включаем платёжную систему Т-Банк (была выключена ACTIVE=N)
        $connection = \Bitrix\Main\Application::getConnection();
        $connection->queryExecute(
            "UPDATE b_sale_pay_system_action SET ACTIVE = 'Y' WHERE ACTION_FILE = 'tinkoff'"
        );
    }

    private function addPropIfNotExists($personTypeId, $propsGroupId, $fields)
    {
        $existing = \CSaleOrderProps::GetList(
            [],
            ['PERSON_TYPE_ID' => $personTypeId, 'CODE' => $fields['CODE']]
        )->Fetch();

        if ($existing) {
            return (int)$existing['ID'];
        }

        $fields['PERSON_TYPE_ID'] = $personTypeId;
        $fields['PROPS_GROUP_ID'] = $propsGroupId;
        $fields['ACTIVE'] = 'Y';

        $id = (int)\CSaleOrderProps::Add($fields);
        if ($id <= 0) {
            throw new \Exception('Не удалось создать свойство заказа ' . $fields['CODE']);
        }
        return $id;
    }

    public function down()
    {
        Loader::includeModule('sale');

        $connection = \Bitrix\Main\Application::getConnection();
        $connection->queryExecute(
            "UPDATE b_sale_pay_system_action SET ACTIVE = 'N' WHERE ACTION_FILE = 'tinkoff'"
        );

        $personTypeId = (int)Option::get(self::OPTION_MODULE_ID, self::OPTION_PERSON_TYPE_ID);
        if ($personTypeId > 0) {
            $props = \CSaleOrderProps::GetList([], ['PERSON_TYPE_ID' => $personTypeId]);
            while ($prop = $props->Fetch()) {
                \CSaleOrderProps::Delete($prop['ID']);
            }

            $groupId = (int)Option::get(self::OPTION_MODULE_ID, self::OPTION_PROPS_GROUP_ID);
            if ($groupId > 0) {
                \CSaleOrderPropsGroup::Delete($groupId);
            }

            \CSalePersonType::Delete($personTypeId);
        }

        Option::delete(self::OPTION_MODULE_ID, ['name' => self::OPTION_PERSON_TYPE_ID]);
        Option::delete(self::OPTION_MODULE_ID, ['name' => self::OPTION_PROPS_GROUP_ID]);
    }
}
