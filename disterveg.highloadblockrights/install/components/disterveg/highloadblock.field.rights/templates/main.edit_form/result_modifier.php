<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var StringUfComponent $component
 * @var array $arResult
 */

$component = $this->getComponent();
$access = new \CAccess;
$accessCodes = [];
$arResult['currentRights'] = $component->getCurrentRights();
foreach ($arResult['additionalParameters']['VALUE'] as $key => $arValue) {
    $accessCodes[] = $arValue['attrList']['value']['ACCESS_CODE'];
}
$currentAccessCodes = array_column($arResult['currentRights'], 'ACCESS_CODE');
$arResult['currentRightsName'] = $access->GetNames($currentAccessCodes);

$arResult['tasks'] = $component->getRightTasks();
foreach ($arResult['tasks'] as $taskId => $taskName) {
    $arResult['taskOptions'][] = ['value' => $taskId, 'text' => $taskName];
}

$arResult['hlRights'] = $component->getHlblockRights();
