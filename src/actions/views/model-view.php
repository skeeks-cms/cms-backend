<?php

use skeeks\cms\backend\widgets\BackendSurfaceWidget;
use yii\widgets\DetailView;

/* @var $model yii\base\Model */
/* @var $attributes array */

echo BackendSurfaceWidget::widget([
    'options' => ['class' => 'sx-model-card'],
    'content' => DetailView::widget([
        'model'      => $model,
        'attributes' => $attributes,
        'options'    => ['class' => 'table sx-detail-view'],
    ]),
]);
