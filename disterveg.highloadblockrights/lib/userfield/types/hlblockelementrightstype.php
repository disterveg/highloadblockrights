<?php

namespace Disterveg\HighloadblockRights\UserField\Types;

use Bitrix\Main\UserField\Types\BaseType,
    Bitrix\Main\Localization\Loc;
use CUserTypeManager;
use Disterveg\HighloadblockRights\Internals\HlblockElementRightsTable;

Loc::loadMessages(__FILE__);

/**
 * Пользовательское кастомное поле
 * Права доступа на highloadblock элементы
 *
 * Class HlBlockElementRightsType
 * @package Disterveg\HighloadblockRights\UserField\Types
 */
class HlBlockElementRightsType extends BaseType
{
    public const
        USER_TYPE_ID = 'hlblock_element_rights',
        RENDER_COMPONENT = 'disterveg:highloadblock.field.rights';

    public static function getDescription(): array
    {
        return [
            'DESCRIPTION' => Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_USER_FIELD'),
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_INT,
        ];
    }

    /**
     * @return string
     */
    public static function getDbColumnType(): string
    {
        return 'int(18)';
    }

    /**
     * @param array $userField
     * @return array
     */
    public static function prepareSettings(array $userField): array
    {
        return [];
    }

    /**
     * @param array $userField
     * @param string|array $value
     * @return array
     */
    public static function checkFields(array $userField, $value): array
    {
        return [];
    }

    /**
     * @param array|bool $userField
     * @param array|null $additionalParameters
     * @param $varsFromForm
     * @return string
     */
    public static function getSettingsHtml($userField, ?array $additionalParameters, $varsFromForm): string
    {
        return '';
    }

    /**
     * @param array $userField
     * @param array $value
     * @return int|null
     * @throws \Exception
     */
    public static function onBeforeSave(array $userField, array $value)
    {
        $request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
        if ($value['DEL'] === 'Y') {
            HlblockElementRightsTable::delete($value['RIGHT_ID']);
            return null;
        }

        if (empty($value['RIGHT_ID'])) {
            $result = HlblockElementRightsTable::add([
                'ELEMENT_ID' => $userField['ENTITY_VALUE_ID'],
                'ACCESS_CODE' => $value['ACCESS_CODE'],
                'TASK_ID' => $value['TASK_ID'],
                'HL_ID' => $request->get('ENTITY_ID')
            ]);
            return $result->getId();
        } else {
            HlblockElementRightsTable::update($value['RIGHT_ID'], [
                'ELEMENT_ID' => $userField['ENTITY_VALUE_ID'],
                'ACCESS_CODE' => $value['ACCESS_CODE'],
                'TASK_ID' => $value['TASK_ID'],
                'HL_ID' => $request->get('ENTITY_ID')
            ]);
            return (int) $value['RIGHT_ID'];
        }
    }
}