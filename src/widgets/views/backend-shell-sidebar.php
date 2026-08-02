<?php
/**
 * @var yii\web\View $this
 * @var string $beforeMenu
 * @var string $menu
 * @var string $afterMenu
 * @var array $options
 * @var array $innerOptions
 */

use yii\helpers\Html;

echo Html::beginTag('aside', $options);
echo Html::beginTag('div', $innerOptions);
echo $beforeMenu;
echo $menu;
echo $afterMenu;
echo Html::endTag('div');
echo Html::endTag('aside');
