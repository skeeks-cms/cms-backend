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
<? if ($action->preContent) : ?>
    <div class="sx-box sx-p-10 sx-bg-primary" style="margin-bottom: 10px;">
        <? echo $action->preContent; ?>
    </div>
<? endif; ?>


<?php $form = $action->beginActiveForm(); ?>
    <?= $form->errorSummary($formModels); ?>

        <? echo (new \skeeks\yii2\form\Builder([
            'model' => $model,
            'models' => $formModels,
            'activeForm' => $form,
            'fields' => $action->fields,
        ]))->render(); ?>

    <?= $form->buttonsStandart($model); ?>
    <?= $form->errorSummary($formModels); ?>
<?php $action->endActiveForm(); ?>
