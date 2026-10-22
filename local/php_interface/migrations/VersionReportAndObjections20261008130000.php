<?php

namespace Sprint\Migration;

class VersionReportAndObjections20261008130000 extends Version
{
    protected $author = "";

    protected $description = "Группа «Менеджер», статус проекта «Реализован», свойства итогового отчёта, иблок «Возражения», email-шаблон смены статуса проекта";

    protected $moduleVersion = "5.15.0";

    const OPTION_OBJECTIONS_IBLOCK_ID = 'grader_objections_iblock_id';
    const OPTION_MANAGER_GROUP_ID = 'grader_manager_group_id';
    const OPTION_EVENT_TYPE = 'grader_project_status_changed';

    public function up()
    {
        $helper = $this->getHelperManager();

        // 1. Группа "Менеджер"
        $groupId = (int)\Bitrix\Main\Config\Option::get('main', self::OPTION_MANAGER_GROUP_ID);
        if ($groupId <= 0) {
            $group = new \CGroup;
            $groupId = (int)$group->Add([
                'ACTIVE' => 'Y',
                'C_SORT' => 100,
                'NAME' => 'Менеджер',
                'DESCRIPTION' => 'Менеджеры проектов: публикуют итоговые отчёты и получают уведомления о сменах статуса проектов',
                'STRING_ID' => 'MANAGER',
            ]);
            if ($groupId <= 0) {
                throw new \Exception('Не удалось создать группу "Менеджер"');
            }
            \Bitrix\Main\Config\Option::set('main', self::OPTION_MANAGER_GROUP_ID, $groupId);
        }

        // Права менеджера на публикацию отчёта проверяются в коде (membership в группе "Менеджер"),
        // а не через классический b_iblock_rights ACL — таблица отсутствует в этой БД-реставрации.

        // 2. Новое значение статуса "Реализован" у свойства PR_STATUS
        $statusPropId = $helper->Iblock()->getPropertyId(PROJECTS_IBLOCK_ID, 'PR_STATUS');
        $existingEnum = \CIBlockPropertyEnum::GetList([], ['PROPERTY_ID' => $statusPropId, 'VALUE' => 'Реализован'])->Fetch();
        if (!$existingEnum) {
            $enumId = \CIBlockPropertyEnum::Add([
                'PROPERTY_ID' => $statusPropId,
                'VALUE' => 'Реализован',
                'DEF' => 'N',
                'SORT' => 250,
            ]);
            if (!$enumId) {
                throw new \Exception('Не удалось добавить статус "Реализован"');
            }
        }

        // 3. Свойства итогового отчёта на проекте
        $helper->Iblock()->saveProperty(PROJECTS_IBLOCK_ID, [
            'CODE' => 'PR_REPORT_TEXT',
            'NAME' => 'Итоговый отчёт: описание работ',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => 10,
            'SORT' => 600,
        ]);
        $helper->Iblock()->saveProperty(PROJECTS_IBLOCK_ID, [
            'CODE' => 'PR_REPORT_AMOUNT',
            'NAME' => 'Итоговый отчёт: сумма расходов',
            'PROPERTY_TYPE' => 'S',
            'SORT' => 610,
        ]);
        $helper->Iblock()->saveProperty(PROJECTS_IBLOCK_ID, [
            'CODE' => 'PR_REPORT_PHOTOS',
            'NAME' => 'Итоговый отчёт: фотографии "после"',
            'PROPERTY_TYPE' => 'F',
            'MULTIPLE' => 'Y',
            'SORT' => 620,
        ]);
        $helper->Iblock()->saveProperty(PROJECTS_IBLOCK_ID, [
            'CODE' => 'PR_REPORT_DATE',
            'NAME' => 'Дата публикации итогового отчёта',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'DateTime',
            'SORT' => 630,
        ]);

        // 4. Инфоблок "Возражения"
        $objectionsIblockId = $helper->Iblock()->addIblockIfNotExists([
            'CODE' => 'objections',
            'IBLOCK_TYPE_ID' => 'services',
            'NAME' => 'Возражения по отчётам проектов',
            'LID' => ['s1'],
            'GROUP_ID' => ['1' => 'X'],
            'VERSION' => 2,
        ]);
        \Bitrix\Main\Config\Option::set('main', self::OPTION_OBJECTIONS_IBLOCK_ID, $objectionsIblockId);

        $helper->Iblock()->saveProperty($objectionsIblockId, [
            'CODE' => 'PROJECT_ID',
            'NAME' => 'Проект',
            'PROPERTY_TYPE' => 'E',
            'LINK_IBLOCK_ID' => PROJECTS_IBLOCK_ID,
            'SORT' => 100,
        ]);
        $helper->Iblock()->saveProperty($objectionsIblockId, [
            'CODE' => 'USER_ID',
            'NAME' => 'Пользователь',
            'PROPERTY_TYPE' => 'N',
            'SORT' => 200,
        ]);
        $helper->Iblock()->saveProperty($objectionsIblockId, [
            'CODE' => 'TEXT',
            'NAME' => 'Текст возражения',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => 10,
            'SORT' => 300,
        ]);
        $helper->Iblock()->saveProperty($objectionsIblockId, [
            'CODE' => 'FILES',
            'NAME' => 'Приложенные файлы',
            'PROPERTY_TYPE' => 'F',
            'MULTIPLE' => 'Y',
            'SORT' => 400,
        ]);

        // 5. Почтовый шаблон для уведомлений о смене статуса проекта
        $eventType = self::OPTION_EVENT_TYPE;
        if (!\CEventType::GetList(['LID' => 's1', 'TYPE_ID' => $eventType])->Fetch()) {
            \CEventType::Add([
                'LID' => 's1',
                'EVENT_NAME' => $eventType,
                'NAME' => 'Смена статуса проекта',
                'DESCRIPTION' => "#PROJECT_NAME# - название проекта\n#OLD_STATUS# - предыдущий статус\n#NEW_STATUS# - новый статус\n#PROJECT_URL# - ссылка на проект\n#EMAIL_TO# - email получателя",
            ]);
        }

        if (!\CEventMessage::GetList([], ['TYPE_ID' => $eventType, 'LID' => 's1'])->Fetch()) {
            \CEventMessage::Add([
                'ACTIVE' => 'Y',
                'EVENT_NAME' => $eventType,
                'LID' => 's1',
                'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                'EMAIL_TO' => '#EMAIL_TO#',
                'SUBJECT' => 'Статус проекта «#PROJECT_NAME#» изменён',
                'BODY_TYPE' => 'text',
                'MESSAGE' => "Статус проекта «#PROJECT_NAME#» изменён: #OLD_STATUS# → #NEW_STATUS#.\n\nПодробнее: #PROJECT_URL#",
            ]);
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $eventType = self::OPTION_EVENT_TYPE;
        $message = \CEventMessage::GetList([], ['TYPE_ID' => $eventType, 'LID' => 's1'])->Fetch();
        if ($message) {
            \CEventMessage::Delete($message['ID']);
        }
        \CEventType::Delete($eventType);

        $objectionsIblockId = (int)\Bitrix\Main\Config\Option::get('main', self::OPTION_OBJECTIONS_IBLOCK_ID);
        if ($objectionsIblockId > 0) {
            $helper->Iblock()->deleteIblock($objectionsIblockId);
        }

        $helper->Iblock()->deletePropertyIfExists(PROJECTS_IBLOCK_ID, 'PR_REPORT_TEXT');
        $helper->Iblock()->deletePropertyIfExists(PROJECTS_IBLOCK_ID, 'PR_REPORT_AMOUNT');
        $helper->Iblock()->deletePropertyIfExists(PROJECTS_IBLOCK_ID, 'PR_REPORT_PHOTOS');
        $helper->Iblock()->deletePropertyIfExists(PROJECTS_IBLOCK_ID, 'PR_REPORT_DATE');

        $statusPropId = $helper->Iblock()->getPropertyId(PROJECTS_IBLOCK_ID, 'PR_STATUS');
        $existingEnum = \CIBlockPropertyEnum::GetList([], ['PROPERTY_ID' => $statusPropId, 'VALUE' => 'Реализован'])->Fetch();
        if ($existingEnum) {
            \CIBlockPropertyEnum::Delete($existingEnum['ID']);
        }

        $groupId = (int)\Bitrix\Main\Config\Option::get('main', self::OPTION_MANAGER_GROUP_ID);
        if ($groupId > 0) {
            $group = new \CGroup;
            $group->Delete($groupId);
        }

        \Bitrix\Main\Config\Option::delete('main', ['name' => self::OPTION_OBJECTIONS_IBLOCK_ID]);
        \Bitrix\Main\Config\Option::delete('main', ['name' => self::OPTION_MANAGER_GROUP_ID]);
    }
}
