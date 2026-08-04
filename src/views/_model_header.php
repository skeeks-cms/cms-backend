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
if (isset($model->image) && $model->image && $model->image->src) {
    $image = $model->image;
} elseif (isset($model->cmsImage) && $model->cmsImage && $model->cmsImage->src) {
    $image = $model->cmsImage;
} elseif (isset($model->logo) && $model->logo && $model->logo->src) {
    $image = $model->logo;
}
/**
 * @var $controller \skeeks\cms\backend\controllers\BackendModelController
 */
$controller = $this->context;

$isEmpty = \skeeks\cms\backend\helpers\BackendUrlHelper::createByParams()->setBackendParamsByCurrentRequest()->isEmptyLayout;
?>
<?php if (!$isEmpty) : ?>
    <div class="sx-back">
        <a class="sx-model-header__back-link" href="<?php echo \yii\helpers\Url::to([$controller->defaultAction]); ?>">
            ←&nbsp;Вернуться назад
        </a>
    </div>
<?php endif; ?>
<div class="sx-model-header sx-model-header--split">
    <div class="sx-model-header__main">
        <div class="sx-model-header__identity">
        <? if ($image) : ?>
            <div class="sx-model-header__media">
            <?php
            $imageSrc = isset($model->cms_image_id)
                ? $image->src
                : \Yii::$app->imaging->getImagingUrl($image->src,
                    new \skeeks\cms\components\imaging\filters\Thumbnail([
                        'm' => \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND
                    ]));
            ?>
            <img class="sx-model-header__image" src="<?php echo \yii\helpers\Html::encode($imageSrc); ?>"/>
            </div>
        <? endif; ?>
        <div class="sx-model-header__content">
        <h1 class="sx-model-header__title">
            <?php echo $controller->modelShowName; ?>
            <? if (isset($model->sx_id) && $model->sx_id) : ?>
                <?
                $sxInfoUpdateClass = (isset($model->is_sx_info_update) && !$model->is_sx_info_update)
                    ? "sx-text--danger"
                    : "sx-text--success";
                $sxInfoUpdateTitle = (isset($model->is_sx_info_update) && !$model->is_sx_info_update)
                    ? "SkeekS ID: {$model->sx_id}. Обновление информации из сервиса SkeekS Товары запрещено"
                    : "SkeekS ID: {$model->sx_id}. Информация обновляется из сервиса SkeekS Товары";
                $sxMarketUrl = isset(\Yii::$app->skeeksSuppliersApi) ? \Yii::$app->skeeksSuppliersApi->getModelUrl($model) : null;
                $sxIcon = "<i class='fas fa-link {$sxInfoUpdateClass}'></i>";
                ?>
                <span class="sx-id sx-model-header__external-id">
                    <?php if ($sxMarketUrl) : ?>
                        <?php echo \yii\helpers\Html::a($sxIcon, $sxMarketUrl, [
                            'target' => '_blank',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'title' => $sxInfoUpdateTitle,
                        ]); ?>
                    <?php else : ?>
                        <span data-toggle='tooltip' title='<?php echo $sxInfoUpdateTitle; ?>'><?php echo $sxIcon; ?></span>
                    <?php endif; ?>
                </span>
            <? endif; ?>
        </h1>
        <div class="sx-small-info sx-model-header__meta">
            <span title="ID записи - уникальный код записи в базе данных." data-toggle="tooltip"><i class="fas fa-key"></i> <?php echo isset($model->id) ? $model->id : ""; ?></span>
            <? if (isset($model->created_at) && $model->created_at) : ?>
                <span data-toggle="tooltip" title="Запись создана в базе: <?php echo \Yii::$app->formatter->asDatetime($model->created_at); ?>"><i
                            class="far fa-clock"></i> <?php echo \Yii::$app->formatter->asDate($model->created_at); ?></span>
            <? endif; ?>
            <? if (isset($model->created_by) && $model->created_by) : ?>
                <span data-toggle="tooltip" title="Запись создана пользователем с ID: <?php echo $model->createdBy->id; ?>"><i
                            class="far fa-user"></i> <?php echo $model->createdBy->shortDisplayName; ?></span>
            <? endif; ?>
        </div>
        </div>
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
        <div class="sx-model-header__side">
            <div class="sx-model-header__actions">
                <?php echo $href; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
