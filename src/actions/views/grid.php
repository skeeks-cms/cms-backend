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
<?php $pjax = \skeeks\cms\widgets\Pjax::begin(); ?>

<?php /*echo $this->render('_search', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]); */?>

<?
$widgetClassName = $action->gridClassName;
?>
<?
echo $widgetClassName::widget((array) $action->gridConfig);
?>
<?php \skeeks\cms\widgets\Pjax::end(); ?>