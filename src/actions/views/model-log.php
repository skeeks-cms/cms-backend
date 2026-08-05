<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 21.12.2017
 */
use skeeks\cms\backend\widgets\BackendSurfaceWidget;

/* @var $this yii\web\View */
/* @var $controller \skeeks\cms\backend\controllers\BackendModelController */
/* @var $action \skeeks\cms\backend\actions\BackendModelCreateAction|\skeeks\cms\backend\actions\IHasActiveForm */
/* @var $model \skeeks\cms\models\CmsLang */
$controller = $this->context;
$action = $controller->action;
?>



<?php $pjax = \skeeks\cms\widgets\Pjax::begin([
    'id' => 'sx-comments',
]); ?>

    <?php echo BackendSurfaceWidget::widget([
        'options' => ['class' => 'sx-model-log-comment'],
        'content' => \skeeks\cms\widgets\admin\CmsCommentWidget::widget([
            'model' => $action->model,
        ]),
    ]); ?>

    <?php echo \skeeks\cms\widgets\admin\CmsLogListWidget::widget([
        'query' => $action->model->getLogs(),
        'is_show_model' => false,
    ]); ?>

<?php $pjax::end(); ?>
