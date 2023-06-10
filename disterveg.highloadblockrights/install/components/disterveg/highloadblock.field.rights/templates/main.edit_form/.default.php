<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Text\HtmlFilter,
    Bitrix\Main\Web\Json;

/**
 * @var StringUfComponent $component
 * @var array $arResult
 */

\CUtil::InitJSCore(['access']);

$hlRights = $arResult['hlRights'];
$currentRights = $arResult['currentRights'];
$currentRightsName = $arResult['currentRightsName'];

$parentAccessCodes = array_column($hlRights, 'ACCESS_CODE');
$currentAccessCodes = array_column($currentRights, 'ACCESS_CODE');
?>
<table width="100%" class="internal" id="<?= $arResult['userField']['FIELD_NAME'] ?>_table" align="center">
    <tbody>
    <tr id="RIGHTS_heading" class="heading">
        <td colspan="2"><?= Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_SUBTITLE') ?></td>
    </tr>
    <?php foreach ($hlRights as $hlRight): ?>
        <tr class="RIGHTS_row_for_<?= $hlRight['ACCESS_CODE'] ?>
            <?= in_array($hlRight['ACCESS_CODE'], $currentAccessCodes, true) ? ' iblock-strike-out' : '' ?>">
            <td style="width:40%!important; text-align:right"><?= $hlRight['USER_NAME'] ?>:</td>
            <td align="left">
                <input type="hidden" value="<?= HtmlFilter::encode($hlRight['RIGHT_ID']) ?>">
                <input type="hidden" value="<?= HtmlFilter::encode($hlRight['ACCESS_CODE']) ?>">
                <input type="hidden" value="<?= HtmlFilter::encode($hlRight['TASK_ID']) ?>">
                <?= $hlRight['OPERATION_NAME'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php foreach ($currentRights as $key => $arValue): ?>
        <tr>
            <?php
            $code = $arValue['ACCESS_CODE'];
            $title = isset($currentRightsName[$code]['provider']) && $currentRightsName[$code]['provider']
                ? $currentRightsName[$code]['provider'] . ': '
                : '';
            $title = htmlspecialcharsbx(
                isset($currentRightsName[$code]) && isset($currentRightsName[$code]['name'])
                    ? $title . $currentRightsName[$code]['name']
                    : $code
            ); ?>
            <td style="width:40%!important; text-align:right; vertical-align:middle"><?= $title ?>:</td>
            <td align="left">
                <input type="hidden" name="<?= $arResult['userField']['FIELD_NAME'] ?>[<?= $key ?>][RIGHT_ID]"
                       value="<?= HtmlFilter::encode($arValue['RIGHT_ID']) ?>">
                <input type="hidden" name="<?= $arResult['userField']['FIELD_NAME'] ?>[<?= $key ?>][ACCESS_CODE]"
                       value="<?= HtmlFilter::encode($code) ?>">
                <select name="<?= $arResult['userField']['FIELD_NAME'] ?>[<?= $key ?>][TASK_ID]">
                    <?php foreach ($arResult['tasks'] as $taskId => $taskName): ?>
                        <option value="<?= HtmlFilter::encode($taskId) ?>"<?= $arValue['TASK_ID'] == $taskId ? ' selected' : '' ?>>
                            <?= $taskName ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a href="javascript:void(0);" data-id="<?=$code?>" class="access-delete"></a>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr id="add_button">
        <td width="40%" align="right"></td>
        <td width="60%" align="left">
            <a href="javascript:void(0)" class="bx-action-href">
                <?= Loc::getMessage('HLBLOCK_ELEMENT_RIGHTS_ADD') ?>
            </a>
        </td>
    </tr>
    </tbody>
</table>

<script type="text/javascript">
  const hlElementsRights = new HighloadblockFieldRights(
      <?=Json::encode([
          'tasksOptions' => $arResult['taskOptions'],
          'parentAccessCodes' => $parentAccessCodes,
          'selected' => array_fill_keys($currentAccessCodes, true),
          'fieldName' => $arResult['userField']['FIELD_NAME'],
      ])?>
  );
  hlElementsRights.init();

  BX.message({
    SITE_ID: 's1'
  });
</script>