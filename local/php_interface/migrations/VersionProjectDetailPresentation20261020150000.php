<?php

namespace Sprint\Migration;

class VersionProjectDetailPresentation20261020150000 extends Version
{
    protected $author = 'Codex';
    protected $description = 'Свойства для новой детальной страницы проекта';
    protected $moduleVersion = '5.15.0';

    public function up()
    {
        $iblockId = (int)\Bitrix\Main\Config\Option::get('main', 'grader_projects_iblock_id', 1);
        if ($iblockId <= 0) {
            $iblockId = 1;
        }

        $helper = $this->getHelperManager();
        $properties = [
            ['CODE' => 'PR_CITY_COAT', 'NAME' => 'Герб населённого пункта', 'PROPERTY_TYPE' => 'F', 'SORT' => 120],
            ['CODE' => 'PR_FUND_GOAL', 'NAME' => 'Цель сбора, ₽', 'PROPERTY_TYPE' => 'S', 'SORT' => 130],
            ['CODE' => 'PR_FUND_DEADLINE', 'NAME' => 'Дата окончания сбора', 'PROPERTY_TYPE' => 'S', 'SORT' => 140],
            ['CODE' => 'PR_HERO_VIDEO', 'NAME' => 'Видео проекта (URL)', 'PROPERTY_TYPE' => 'S', 'SORT' => 150],
            ['CODE' => 'PR_MILESTONES', 'NAME' => 'Этапы проекта', 'PROPERTY_TYPE' => 'S', 'USER_TYPE' => 'HTML', 'MULTIPLE' => 'N', 'SORT' => 160],
        ];

        foreach ($properties as $property) {
            $helper->Iblock()->saveProperty($iblockId, $property);
        }
    }

    public function down()
    {
        // Свойства намеренно не удаляются автоматически: это безопаснее для
        // уже заполненных карточек проектов. При откате их можно удалить из админки.
    }
}
