<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 21.12.2017
 */
/* @var $this yii\web\View */
/* @var $controller \skeeks\cms\backend\controllers\BackendModelController */
/* @var $action \skeeks\cms\backend\actions\BackendModelCreateAction|\skeeks\cms\backend\actions\IHasActiveForm */
/* @var $model \skeeks\cms\models\CmsLang */
$controller = $this->context;
$action = $controller->action;

?>

<? if ($action->beforeContent) : ?>
    <div class="sx-box sx-p-10 sx-bg-primary" style="margin-bottom: 10px;">
        <? echo $action->beforeContent; ?>
    </div>
<? endif; ?>

<?php $pjax = \skeeks\cms\widgets\Pjax::begin(); ?>
<?php $form = $action->beginActiveForm(); ?>

<?= $form->errorSummary($formModels); ?>

<? echo \Yii::createObject([
    'class'      => \skeeks\yii2\form\Builder::class,
    'model'      => $model,
    'models'     => $formModels,
    'activeForm' => $form,
    'fields'     => $action->fields,
])->render(); ?>

<?
echo $form->buttonsStandart($model, $action->buttons); ?>
<?= $form->errorSummary($formModels); ?>

<?php $form::end(); ?>

<?php $pjax::end(); ?>


<? if ($action->afterContent) : ?>
    <div class="sx-box sx-p-10 sx-bg-primary" style="margin-bottom: 10px;">
        <? echo $action->afterContent; ?>
    </div>
<? endif; ?>
