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


<?
$widgetClassName = $action->gridClassName;
$widgetFiltersClassName = $action->filtersClassName;
?>

<?
/**
 *
 */
$grid = $widgetClassName::begin((array)$action->gridConfig);
?>

<?
$filtersConfig = (array)$action->filtersConfig;
$filtersConfig['dataProvider'] = $grid->dataProvider;

$component = $widgetFiltersClassName::begin($filtersConfig);
$widgetFiltersClassName::end();
?>


        <!--
<? /*$component = $component->configModel; */ ?>
<?php /*$form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin(); */ ?>

<? /*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
)); */ ?>

<? /* if ($fields = $component->builderFields()) : */ ?>
    <? /* echo (new \skeeks\yii2\form\Builder([
        'models'     => $component->builderModels(),
        'model'      => $component,
        'activeForm' => $form,
        'fields'     => $fields,
    ]))->render(); */ ?>
<? /* elseif ($formContent = $component->renderConfigForm($form)) : */ ?>
    <? /*= $formContent; */ ?>
<? /* else : */ ?>
    Нет редактируемых настроек для данного компонента
<? /* endif; */ ?>

<? /*= $form->buttonsStandart($component); */ ?>
<? /*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
)); */ ?>

--><?php /*\skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end(); */ ?>


<?
$widgetClassName::end();
?>
<? if (YII_ENV === 'dev') : ?>
    <pre><?= $component->dataProvider->query->createCommand()->rawSql; ?></pre>
<? endif; ?>
<?php \skeeks\cms\widgets\Pjax::end(); ?>