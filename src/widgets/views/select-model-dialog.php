<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 11.07.2015
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\backend\widgets\SelectModelDialogWidget */
/* @var $input string */
$widget = $this->context;
?>
<div class="row" id="<?= $widget->id; ?>">
    <div class="col-lg-12">
        <div class="row sx-one-input">
            <? if ($widget->visibleInput) : ?>
                <div class="col-lg-3">
                    <?= $input; ?>
                </div>
            <? else : ?>
                <div style="display: none;">
                    <?= $input; ?>
                </div>
            <? endif; ?>
            <div class="col-lg-6">

                <span class="sx-view-cms-content">
                    <? if ($widget->previewValue) : ?>
                        <?= $widget->previewValue; ?>
                    <? endif; ?>
                </span>

                <a class="btn btn-default sx-btn-create" title="Выбрать">
                    <i class="glyphicon glyphicon-th-list" title="Выбрать"></i>
                </a>
                <? if ($widget->allowDeselect) : ?>
                    <a class="btn btn-default btn-danger sx-btn-deselect" <?= !$widget->hasValue() ? "style='display: none;'": ""?> title="Убрать выбранное">
                        <i class="glyphicon glyphicon-remove"></i>
                    </a>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>
<?
$jsonOptions = \yii\helpers\Json::encode($widget->clientOptions);
$this->registerJs(<<<JS
(function(sx, $, _)
{
    new sx.classes.SelectModelDialog({$jsonOptions});
})(sx, sx.$, sx._);
JS
)
?>