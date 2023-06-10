<?php

namespace Disterveg\HighloadblockRights\Helpers;

use Bitrix\Highloadblock\HighloadBlockTable,
    Bitrix\Main\DB\SqlExpression;
use Disterveg\HighloadblockRights\Internals\HlblockElementRightsTable;

/**
 * Хелпер для работы с правами на элементы
 *
 * Class HlblockRights
 * @package Disterveg\HighloadblockRights\Helpers
 */
class HlblockRights
{
    private $entityDataClass;
    private $entity;
    private $hlblock;

    /**
     * HlblockRights constructor.
     * @param $hlblock название сущности или id хайлоада, название таблицы
     */
    public function __construct($hlblock)
    {
        $this->hlblock = HighloadBlockTable::resolveHighloadblock($hlblock);
        $this->entity = HighloadBlockTable::compileEntity($this->hlblock);
        $this->entityDataClass = $this->entity->getDataClass();
    }

    /**
     *  * Получить элементы отфильтрованные по правам
     *
     * @param array $parameters параметры к выборке
     * @param int $permissionsBy права по пользователю
     * @param string $minPermission мин уровень прав
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function getItemsFilteredByPermissions(
        array $parameters,
        string $minPermission = 'read',
        int $permissionsBy = 0
    ): array
    {
        $filter = $this->prepareFilter($parameters, $minPermission, $permissionsBy);
        $query = $this->entityDataClass::getList($this->setParameters($parameters, $filter));
        $result = $query->fetchAll();

        return $result ?? [];
    }

    /**
     * Установить параметры
     *
     * @param array $parameters
     * @param array $filter
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    private function setParameters(array $parameters, array $filter): array
    {
        $parameters['filter'] = $filter;
        $parameters['runtime']['HLBLOCK'] = [
            'data_type' => HighloadBlockTable::getEntity(),
            'reference' => [
                '=ref.TABLE_NAME' => new \Bitrix\Main\DB\SqlExpression('?s', $this->hlblock['TABLE_NAME']),
            ],
        ];
        $parameters['runtime']['HL_RIGHTS'] = [
            'data_type' => \Bitrix\HighloadBlock\HighloadBlockRightsTable::getEntity(),
            'reference' => [
                '=this.HLBLOCK.ID' => 'ref.HL_ID',
                'ref.HL_ID' => new SqlExpression('?s', $this->hlblock['ID']),
            ],
        ];
        $parameters['runtime']['ELEMENT_RIGHTS'] = [
            'data_type' => HlblockElementRightsTable::getEntity(),
            'reference' => [
                '=this.HLBLOCK.ID' => 'ref.HL_ID',
                '=this.ID' => 'ref.ELEMENT_ID'
            ],
        ];

        return $parameters;
    }

    /**
     * Подготовить фильтр
     *
     * @param array $parameters
     * @param string $minPermission
     * @param int $permissionsBy
     * @return array
     */
    private function prepareFilter(array $parameters, string $minPermission, int $permissionsBy): array
    {
        $uid = empty($permissionsBy) ? \Bitrix\Main\Engine\CurrentUser::get()->getId() : $permissionsBy;
        $filter = $parameters['filter'];
        $operations = [
            'hl_element_read',
            'hl_element_write'
        ];
        if ($minPermission === 'write') {
            $operations = [
                'hl_element_write'
            ];
        }

        $filter[] = [
            'LOGIC' => 'OR',
            [
                'HL_RIGHTS.USER_ACCESS.USER_ID' => $uid,
                'HL_RIGHTS.TASK_OPERATION.OPERATION.NAME' => $operations,
                'ELEMENT_RIGHTS.ID' => null
            ],
            [
                'ELEMENT_RIGHTS.USER_ACCESS.USER_ID' => $uid,
                'ELEMENT_RIGHTS.TASK_OPERATION.OPERATION.NAME' => $operations
            ]
        ];

        return $filter;
    }
}