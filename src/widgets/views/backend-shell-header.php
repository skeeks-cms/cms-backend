<?php
/**
 * @var yii\web\View $this
 * @var string $brand
 * @var string $context
 * @var string $actions
 * @var string $profile
 * @var array $options
 * @var array $surfaceOptions
 * @var array $navOptions
 * @var array $brandOptions
 * @var array $contextOptions
 * @var array $actionsOptions
 */

use yii\helpers\Html;

echo Html::beginTag('header', $options);
echo Html::beginTag('div', $surfaceOptions);
echo Html::beginTag('nav', $navOptions);

echo Html::tag('div', $brand, $brandOptions);
if ($context !== '') {
    echo Html::tag('div', $context, $contextOptions);
}
echo Html::tag('div', $actions . $profile, $actionsOptions);

echo Html::endTag('nav');
echo Html::endTag('div');
echo Html::endTag('header');
