<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Component\BaseUfComponent;
use Disterveg\HighloadblockRights\UserField\Types\HlBlockElementRightsType;
use Disterveg\HighloadblockRights\Internals\HlblockElementRightsTable;

/**
 * Права доступа на highloadblock элементы
 *
 * Class HighloadblockFieldRights
 */
class HighloadblockFieldRights extends BaseUfComponent
{
    protected static function getUserTypeId(): string
    {
        return HlBlockElementRightsType::USER_TYPE_ID;
    }

    /**
     * Получить права HL блока
     *
     * @return array
     */
    public function getHlblockRights(): array
    {
        $access = new \CAccess;
        $tasks = $this->getRightTasks();
        $accessCodes = [];
        $currentRights = [];
        $res = \Bitrix\HighloadBlock\HighloadBlockRightsTable::getList([
            'filter' => ['HL_ID' => $this->request->get('ENTITY_ID')]
        ]);
        while ($row = $res->fetch()) {
            $currentRights[$row['ID']] = array(
                'RIGHT_ID' => $row['ID'],
                'ACCESS_CODE' => $row['ACCESS_CODE'],
                'TASK_ID' => $row['TASK_ID'],
                'OPERATION_NAME' => $tasks[$row['TASK_ID']]
            );
            $accessCodes[] = $row['ACCESS_CODE'];
        }

        $currentRightsName = $access->GetNames($accessCodes);
        foreach ($currentRights as $rightId => $right) {
            $code = $right['ACCESS_CODE'];
            $title = isset($currentRightsName[$code]['provider']) && $currentRightsName[$code]['provider']
                ? $currentRightsName[$code]['provider'] . ': '
                : '';
            $title = htmlspecialcharsbx(
                isset($currentRightsName[$code]) && isset($currentRightsName[$code]['name'])
                    ? $title . $currentRightsName[$code]['name']
                    : $code
            );
            $currentRights[$rightId]['USER_NAME'] = $title;
        }

        return $currentRights;
    }

    /**
     * Получить права
     *
     * @return array
     */
    public function getRightTasks(): array
    {
        $tasks = [];
        $res = \CTask::GetList(['LETTER' => 'ASC'], ['MODULE_ID' => ADMIN_MODULE_NAME]);
        while ($row = $res->getNext()) {
            $tasks[$row['ID']] = $row['TITLE'];
        }

        return $tasks;
    }

    /**
     * Получить текущие права
     *
     * @return array
     */
    public function getCurrentRights(): array
    {
        $access = new \CAccess;
        $tasks = $this->getRightTasks();
        $accessCodes = [];
        $currentRights = [];
        $res = HlblockElementRightsTable::getList([
            'filter' => ['ELEMENT_ID' => $this->request->get('ID')]
        ]);
        while ($row = $res->fetch()) {
            $currentRights[$row['ID']] = array(
                'RIGHT_ID' => $row['ID'],
                'ACCESS_CODE' => $row['ACCESS_CODE'],
                'TASK_ID' => $row['TASK_ID'],
                'OPERATION_NAME' => $tasks[$row['TASK_ID']]
            );
            $accessCodes[] = $row['ACCESS_CODE'];
        }
        $currentRightsName = $access->GetNames($accessCodes);
        foreach ($currentRights as $rightId => $right) {
            $code = $right['ACCESS_CODE'];
            $title = isset($currentRightsName[$code]['provider']) && $currentRightsName[$code]['provider']
                ? $currentRightsName[$code]['provider'] . ': '
                : '';
            $title = htmlspecialcharsbx(
                isset($currentRightsName[$code]) && isset($currentRightsName[$code]['name'])
                    ? $title . $currentRightsName[$code]['name']
                    : $code
            );
            $currentRights[$rightId]['USER_NAME'] = $title;
        }

        return $currentRights;
    }

    public function getRightById($id)
    {
        $res = HlblockElementRightsTable::getRow(['filter' => ['ID' => $id]]);
        if (!empty($res)) {
            return $res;
        }

        return [];
    }
}