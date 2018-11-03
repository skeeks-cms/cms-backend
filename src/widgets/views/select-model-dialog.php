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
<?= \yii\helpers\Html::beginTag("div", $widget->wrapperOptions); ?>
    <div class="col-lg-12">
        <div class="row sx-one-input">


            <? if ($widget->multiple) : ?>

                <div style="display: none;">
                    <?= $input; ?>
                </div>
                <div class="col-lg-12">
                    <ul class="sx-view-cms-content">

                    </ul>
                </div>

                <div class="col-lg-12">
                    <?= \yii\helpers\Html::tag($widget->selectBtn['tag'], $widget->selectBtn['content'], $widget->selectBtn['options'])?>
                    <!--<a class="btn btn-default sx-btn-create sx-btn-create" title="Добавить значение">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </a>-->
                    <? if ($widget->allowDeselect) : ?>
                        <a class="btn btn-default btn-danger btn-xs sx-btn-deselect" <?= !$widget->hasValue() ? "style='display: none;'": ""?> title="Очистить выбранное">
                            <i class="fa fa-times"></i>
                        </a>
                    <? endif; ?>
                </div>

            <? else : ?>

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

                    </span>

                    <?= \yii\helpers\Html::tag($widget->selectBtn['tag'], $widget->selectBtn['content'], $widget->selectBtn['options'])?>
                    <!--
                    <a class="btn btn-default sx-btn-create sx-btn-create" title="Выбрать">
                        <i class="fa fa-list" title="Выбрать"></i>
                    </a>-->
                    <? if ($widget->allowDeselect) : ?>
                        <a class="btn btn-default btn-danger btn-xs sx-btn-deselect" <?= !$widget->hasValue() ? "style='display: none;'": ""?> title="Очистить выбранное">
                            <i class="fa fa-times"></i>
                        </a>
                    <? endif; ?>
                </div>


                <? if ($widget->multiple) : ?>
                    <div class="col-lg-12">
                        <?= \yii\helpers\Html::tag($widget->selectBtn['tag'], $widget->selectBtn['content'], $widget->selectBtn['options'])?>
                        <!--
                        <a class="btn btn-default sx-btn-create sx-btn-create" title="Добавить значение">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </a>-->
                        <? if ($widget->allowDeselect) : ?>
                            <a class="btn btn-default btn-danger btn-xs sx-btn-deselect" <?= !$widget->hasValue() ? "style='display: none;'": ""?> title="Очистить выбранное">
                                <i class="fa fa-times"></i>
                            </a>
                        <? endif; ?>
                    </div>
                <? endif; ?>

            <? endif; ?>


        </div>
    </div>
<?= \yii\helpers\Html::endTag("div"); ?>
<?
$jsonOptions = \yii\helpers\Json::encode($widget->clientOptions);

if ($widget->multiple)
{
    $this->registerJs(<<<JS
    (function(sx, $, _)
    {
        new sx.classes.SelectModelDialogMultiple({$jsonOptions});
    })(sx, sx.$, sx._);
JS
    );
} else
{
    $this->registerJs(<<<JS
    (function(sx, $, _)
    {
        new sx.classes.SelectModelDialog({$jsonOptions});
    })(sx, sx.$, sx._);
JS
    );
}

?>