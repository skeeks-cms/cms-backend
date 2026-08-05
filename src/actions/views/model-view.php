<?php

use skeeks\cms\backend\assets\BackendPanelAsset;
use yii\widgets\DetailView;

/* @var $model yii\base\Model */
/* @var $attributes array */

BackendPanelAsset::register($this);
?>

<div class="sx-panel sx-panel--padded sx-model-card">
    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => $attributes,
        'options'    => ['class' => 'table sx-detail-view'],
    ]); ?>
</div>
