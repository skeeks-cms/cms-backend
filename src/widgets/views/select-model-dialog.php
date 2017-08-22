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
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                    <?= $input; ?>
                </div>
            <? else : ?>
                <div style="display: none;">
                    <?= $input; ?>
                </div>
            <? endif; ?>
            <div class="col-lg-9 col-md-9 col-sm-6 col-xs-6">

                <span class="sx-view-cms-content">
                    <? if ($widget->previewValue) : ?>
                        <?= $widget->previewValue; ?>
                    <? endif; ?>
                </span>

                <? if (!$widget->multiple) : ?>
                    <a class="btn btn-default sx-btn-create sx-btn-create" title="Выбрать">
                        <i class="glyphicon glyphicon-th-list" title="Выбрать"></i>
                    </a>
                    <? if ($widget->allowDeselect) : ?>
                        <a class="btn btn-default btn-danger sx-btn-deselect" <?= !$widget->hasValue() ? "style='display: none;'": ""?> title="Очистить выбранное">
                            <i class="glyphicon glyphicon-remove"></i>
                        </a>
                    <? endif; ?>
                <? endif; ?>
            </div>

            <? if ($widget->multiple) : ?>
                <div class="col-lg-12">
                    <a class="btn btn-default sx-btn-create sx-btn-create" title="Добавить значение">
                        <i class="glyphicon glyphicon-th-list" aria-hidden="true"></i>
                    </a>
                    <? if ($widget->allowDeselect) : ?>
                        <a class="btn btn-default btn-danger sx-btn-deselect" <?= !$widget->hasValue() ? "style='display: none;'": ""?> title="Очистить выбранное">
                            <i class="glyphicon glyphicon-remove"></i>
                        </a>
                    <? endif; ?>
                </div>
            <? endif; ?>

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