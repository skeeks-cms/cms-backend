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
<?php $pjax = \yii\widgets\Pjax::begin(); ?>

<?php /*echo $this->render('_search', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]); */?>
<?
$gridClassName = $action->gridClassName;
?>
<?= $gridClassName::widget($action->gridConfig); ?>

<?php \yii\widgets\Pjax::end(); ?>