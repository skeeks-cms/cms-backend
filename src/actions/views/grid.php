<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */
/* @var $this yii\web\View */
/* @var $action \skeeks\cms\backend\actions\BackendGridModelAction */
$controller = $this->context;
$action = $controller->action;
?>
<?= $action->backendShowing->displayName; ?>

<?php $pjax = \skeeks\cms\widgets\Pjax::begin(); ?>

<?php /*echo $this->render('_search', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]); */?>

<?php $form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    'layout' => 'horizontal',
]); ?>
<?
$action->filters->load(\Yii::$app->request->get());

echo (new \skeeks\yii2\form\Builder([
    'models'     => $action->filters->builderModels(),
    'model'      => $action->filters,
    'activeForm' => $form,
    'fields'     => $action->filters->builderFields(),
]))->render(); ?>

<? \yii\bootstrap\ActiveForm::end();?>
<?
$widgetClassName = $action->gridClassName;
?>
<?
/**
 *
 */
$component = $widgetClassName::begin((array) $action->gridConfig);
?>

<!--
<?/*$component = $component->configModel; */?>
<?php /*$form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin(); */?>

<?/*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
)); */?>

<?/* if ($fields = $component->builderFields()) : */?>
    <?/* echo (new \skeeks\yii2\form\Builder([
        'models'     => $component->builderModels(),
        'model'      => $component,
        'activeForm' => $form,
        'fields'     => $fields,
    ]))->render(); */?>
<?/* elseif ($formContent = $component->renderConfigForm($form)) : */?>
    <?/*= $formContent; */?>
<?/* else : */?>
    Нет редактируемых настроек для данного компонента
<?/* endif; */?>

<?/*= $form->buttonsStandart($component); */?>
<?/*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
)); */?>

--><?php /*\skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end(); */?>


<?
$widgetClassName::end();
?>
<?php \skeeks\cms\widgets\Pjax::end(); ?>