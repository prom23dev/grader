<?php

namespace Sprint\Migration;

class VersionAddUserEmailNotifyField20261021120000 extends Version
{
    protected $author = "";

    protected $description = "Добавляет поле UF_EMAIL_NOTIFY пользователю — переключатель 'Получать e-mail рассылку' в личном кабинете (/personal/settings/)";

    protected $moduleVersion = "5.15.0";

    /**
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->saveUserTypeEntity([
            'ENTITY_ID'         => 'USER',
            'FIELD_NAME'        => 'UF_EMAIL_NOTIFY',
            'USER_TYPE_ID'      => 'boolean',
            'XML_ID'            => 'UF_EMAIL_NOTIFY',
            'SORT'              => 500,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_IN_LIST'      => 'Y',
            'EDIT_IN_LIST'      => 'Y',
            'IS_SEARCHABLE'     => 'N',
            'SETTINGS'          => ['DEFAULT_VALUE' => 1],
            'EDIT_FORM_LABEL'   => ['ru' => 'Получать e-mail рассылку', 'en' => 'Receive e-mail notifications'],
            'LIST_COLUMN_LABEL' => ['ru' => 'E-mail рассылка', 'en' => 'E-mail notifications'],
            'LIST_FILTER_LABEL' => ['ru' => 'E-mail рассылка', 'en' => 'E-mail notifications'],
        ]);

        // Кастомные поля сущности USER хранятся в b_uts_user, а не колонкой
        // в b_user — отдельный backfill не нужен: SETTINGS.DEFAULT_VALUE=1
        // уже заставляет CUser::GetByID() отдавать 1 для пользователей без
        // своей строки в b_uts_user.
    }

    /**
     * @throws Exceptions\HelperException
     */
    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('USER', 'UF_EMAIL_NOTIFY');
    }
}
