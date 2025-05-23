<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */
/**
 * @var $this yii\web\View
 * @var $model \skeeks\cms\models\CmsContentElement
 */

$image = null;
if (isset($model->image)) {
    $image = $model->image;
} elseif (isset($model->cmsImage)) {
    $image = $model->cmsImage;
}
/**
 * @var $controller \skeeks\cms\backend\controllers\BackendModelController
 */
$controller = $this->context;

$isEmpty = \skeeks\cms\backend\helpers\BackendUrlHelper::createByParams()->setBackendParamsByCurrentRequest()->isEmptyLayout;
?>
<?php if (!$isEmpty) : ?>
    <div class="sx-back">
        <a href="<?php echo \yii\helpers\Url::to([$controller->defaultAction]); ?>" style="    font-style: normal;
    font-weight: 400;
    font-size: 12px;
    line-height: 26px;
    color: #656464;">
            ←&nbsp;Вернуться назад
        </a>
    </div>
<?php endif; ?>
<div class="row no-gutters" style="margin-bottom: 5px;">
    <? if ($image) : ?>
        <div class="col my-auto" style="max-width: 60px">
            <img style="border: 2px solid #ededed; border-radius: 5px;" src="<?php echo \Yii::$app->imaging->getImagingUrl($image->src,
                new \skeeks\cms\components\imaging\filters\Thumbnail([
                    'm' => \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND
                ])); ?>"/>
        </div>
    <? endif; ?>
    <div class="col my-auto">
        <h1 style="margin-bottom: 0px; line-height: 1.1;">
            <?php echo $controller->modelShowName; ?>
            <? if (isset($model->sx_id) && $model->sx_id) : ?>
                <span class="sx-id" style="font-size: 17px; font-weight: bold;">
                    <span data-toggle='tooltip' title='SkeekS Suppliers ID: <?php echo $model->sx_id; ?>'><i class='fas fa-link'></i></span>
                </span>
            <? endif; ?>
        </h1>
        <div class="sx-small-info" style="font-size: 10px; color: silver;">
            <span title="ID записи - уникальный код записи в базе данных." data-toggle="tooltip"><i class="fas fa-key"></i> <?php echo isset($model->id) ? $model->id : ""; ?></span>
            <? if (isset($model->created_at) && $model->created_at) : ?>
                <span style="margin-left: 5px;" data-toggle="tooltip" title="Запись создана в базе: <?php echo \Yii::$app->formatter->asDatetime($model->created_at); ?>"><i
                            class="far fa-clock"></i> <?php echo \Yii::$app->formatter->asDate($model->created_at); ?></span>
            <? endif; ?>
            <? if (isset($model->created_by) && $model->created_by) : ?>
                <span style="margin-left: 5px;" data-toggle="tooltip" title="Запись создана пользователем с ID: <?php echo $model->createdBy->id; ?>"><i
                            class="far fa-user"></i> <?php echo $model->createdBy->shortDisplayName; ?></span>
            <? endif; ?>
        </div>
    </div>

    <?php

    $modelActions = $controller->modelActions;
    $deleteAction = \yii\helpers\ArrayHelper::getValue($modelActions, "delete");

    if($deleteAction) : ?>
        <?php

            $actionData = [
                "url"               => $deleteAction->url,

                //TODO:// is deprecated
                "isOpenNewWindow"   => true,
                "confirm"           => isset($deleteAction->confirm) ? $deleteAction->confirm : "",
                "method"            => isset($deleteAction->method) ? $deleteAction->method : "",
                "request"           => isset($deleteAction->request) ? $deleteAction->request : "",
                "size"           => isset($deleteAction->size) ? $deleteAction->size : "",
            ];
            $actionData = \yii\helpers\Json::encode($actionData);

            $href = \yii\helpers\Html::a('<i class="fa fa-trash sx-action-icon"></i>', "#", [
                'onclick' => "new sx.classes.backend.widgets.Action({$actionData}).go(); return false;",
                'class' => "btn btn-default",
                'data-toggle' => "tooltip",
                'title' => "Удалить"
            ]);
        ?>
        <div class="col my-auto" style="text-align: right; max-width: 70px;">
            <?php echo $href; ?>
        </div>
    <?php endif; ?>



    <!--<div class="col my-auto" style="max-width: 70px; text-align: right;">
            <a href="<?php /*echo $model->url; */ ?>" data-toggle="tooltip" class="btn btn-default" target="_blank" title="<?php /*echo \Yii::t('skeeks/cms', 'Watch to site (opens new window)'); */ ?>"><i class="fas fa-external-link-alt"></i></a>
        </div>-->
</div>
