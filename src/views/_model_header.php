<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

use skeeks\cms\backend\widgets\BackendModelHeader;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var object $model
 */

$titleSuffix = '';
if (isset($model->sx_id) && $model->sx_id) {
    $isUpdateDisabled = isset($model->is_sx_info_update) && !$model->is_sx_info_update;
    $stateClass = $isUpdateDisabled ? 'sx-text--danger' : 'sx-text--success';
    $stateTitle = $isUpdateDisabled
        ? "SkeekS ID: {$model->sx_id}. Обновление информации из сервиса SkeekS Товары запрещено"
        : "SkeekS ID: {$model->sx_id}. Информация обновляется из сервиса SkeekS Товары";
    $marketUrl = isset(Yii::$app->skeeksSuppliersApi)
        ? Yii::$app->skeeksSuppliersApi->getModelUrl($model)
        : null;
    $icon = Html::tag('i', '', ['class' => "fas fa-link {$stateClass}"]);
    $content = $marketUrl
        ? Html::a($icon, $marketUrl, [
            'target'      => '_blank',
            'data-pjax'   => '0',
            'data-toggle' => 'tooltip',
            'title'       => $stateTitle,
        ])
        : Html::tag('span', $icon, [
            'data-toggle' => 'tooltip',
            'title'       => $stateTitle,
        ]);
    $titleSuffix = Html::tag('span', $content, ['class' => 'sx-id sx-model-header__external-id']);
}

echo BackendModelHeader::widget([
    'model'       => $model,
    'titleSuffix' => $titleSuffix,
]);
