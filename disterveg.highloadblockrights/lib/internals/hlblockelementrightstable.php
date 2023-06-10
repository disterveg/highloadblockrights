<?php

namespace Disterveg\HighloadblockRights\Internals;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\IntegerField;

/**
 * ORM сущность для таблицы права на хайлоад элементы
 *
 * Class HlblockElementRightsTable
 * @package Disterveg\HighloadblockRights\Internals
 */
class HlblockElementRightsTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'dg_hlblock_element_rights';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID', []))
                ->configureTitle(Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true),
            'ELEMENT_ID' => (new StringField('ELEMENT_ID', []))
                ->configureTitle(Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_ELEMENT_ID_FIELD'))
                ->configureRequired(true),
            'HL_ID' => (new StringField('HL_ID', []))
                ->configureTitle(Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_HL_ID_FIELD'))
                ->configureRequired(true),
            'TASK_ID' => (new StringField('TASK_ID', []))
                ->configureTitle(Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_TASK_ID_FIELD'))
                ->configureRequired(true),
            'ACCESS_CODE' => (new StringField('ACCESS_CODE', []))
                ->configureTitle(Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_ACCESS_CODE_FIELD'))
                ->configureRequired(true),
            'USER_ACCESS' => new \Bitrix\Main\Entity\ReferenceField(
                'USER_ACCESS',
                '\Bitrix\Main\UserAccessTable',
                ['=this.ACCESS_CODE' => 'ref.ACCESS_CODE']
            ),
            'TASK_OPERATION' => new \Bitrix\Main\Entity\ReferenceField(
                'TASK_OPERATION',
                '\Bitrix\Main\TaskOperationTable',
                ['=this.TASK_ID' => 'ref.TASK_ID']
            ),
        ];
    }
}