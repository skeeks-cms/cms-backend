<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 27.03.2015
 *
 * @var $component \skeeks\cms\base\Component
 * @var $message string
 */
?>

<? $model = $component->configModel; ?>
<?php $form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin([
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
]); ?>

<? if ($error) : ?>
    <?= \yii\bootstrap\Alert::widget([
        'body' => $error,
        'options' => [
            'class' => 'alert-danger'
        ]
    ]); ?>
<? endif; ?>
<? if ($success) : ?>
    <?= \yii\bootstrap\Alert::widget([
        'body' => $success,
        'options' => [
            'class' => 'alert-success'
        ]
    ]); ?>
<? endif; ?>
<?= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$model], $model->builderModels()
));  ?>

<?  if ($fields = $model->builderFields()) : ?>
    <?  echo (new \skeeks\yii2\form\Builder([
        'models'     => $model->builderModels(),
        'model'      => $model,
        'activeForm' => $form,
        'fields'     => $model->builderFields(),
    ]))->render();  ?>
<?  else :  ?>
    Нет редактируемых настроек для данного компонента
<?  endif;  ?>

<?= $form->buttonsStandart($model);  ?>
<?= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$model], $model->builderModels()
));  ?>

<?php \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end(); ?>
