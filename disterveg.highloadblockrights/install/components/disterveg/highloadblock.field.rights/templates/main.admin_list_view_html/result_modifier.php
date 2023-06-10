<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var $arResult array
 */

$component = $this->getComponent();
$right = $component->getRightById($arResult['additionalParameters']['VALUE']);
$tasks = $component->getRightTasks();

$access = new \CAccess;
$accessCodes = [];
$currentRightsName = $access->GetNames([$right['ACCESS_CODE']]);

$code = $right['ACCESS_CODE'];
$title = isset($currentRightsName[$code]['provider']) && $currentRightsName[$code]['provider']
    ? $currentRightsName[$code]['provider'] . ': '
    : '';
$title = htmlspecialcharsbx(
    isset($currentRightsName[$code]) && isset($currentRightsName[$code]['name'])
        ? $title . $currentRightsName[$code]['name']
        : $code
);

$arResult['additionalParameters']['DISPLAY_VALUE']['ACCESS_CODE'] = $title;
$arResult['additionalParameters']['DISPLAY_VALUE']['TASK'] = $tasks[$right['TASK_ID']];
