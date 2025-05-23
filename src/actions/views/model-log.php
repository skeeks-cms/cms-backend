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



<?php $pjax = \skeeks\cms\widgets\Pjax::begin([
    'id' => 'sx-comments',
]); ?>

    <div class="row">
        <div class="col-12">
            <div class="sx-block">
                <?php echo \skeeks\cms\widgets\admin\CmsCommentWidget::widget([
                    'model' => $action->model,
                ]); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php echo \skeeks\cms\widgets\admin\CmsLogListWidget::widget([
                'query' => $action->model->getLogs(),
                'is_show_model' => false
            ]);; ?>
        </div>
    </div>

<?php $pjax::end(); ?>