<?php

$autoloadCandidates = [
    '/app/vendor/autoload.php',
    dirname(__DIR__).'/vendor/autoload.php',
    dirname(__DIR__, 3).'/autoload.php',
];

foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
        break;
    }
}

$yiiCandidates = [
    '/app/vendor/yiisoft/yii2/Yii.php',
    dirname(__DIR__).'/vendor/yiisoft/yii2/Yii.php',
    dirname(__DIR__, 3).'/yiisoft/yii2/Yii.php',
];
foreach ($yiiCandidates as $yiiBootstrap) {
    if (is_file($yiiBootstrap)) {
        require_once $yiiBootstrap;
        break;
    }
}

use skeeks\cms\backend\assets\BackendPanelAsset;
use skeeks\cms\backend\assets\BackendUiAsset;

Yii::setAlias('@skeeks/cms/backend', dirname(__DIR__).'/src');

function panelExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$uiAsset = (new ReflectionClass(BackendUiAsset::class))->newInstanceWithoutConstructor();
$panelAsset = (new ReflectionClass(BackendPanelAsset::class))->newInstanceWithoutConstructor();

panelExpect(!in_array(BackendPanelAsset::class, (array)$uiAsset->depends, true), 'Panel leaked into BackendUiAsset.');
panelExpect(in_array(BackendUiAsset::class, (array)$panelAsset->depends, true), 'Panel does not build on BackendUiAsset.');
panelExpect($panelAsset->css === ['panel.css'], 'Panel asset must own only panel.css.');

$themeCss = file_get_contents(dirname(__DIR__).'/src/assets/src/theme.css');
$panelCss = file_get_contents(dirname(__DIR__).'/src/assets/src/panel.css');
$modelView = file_get_contents(dirname(__DIR__).'/src/actions/views/model-view.php');

panelExpect(strpos($themeCss, '.sx-panel {') === false, 'Structural panel CSS remains in the global theme.');
panelExpect(strpos($panelCss, '.sx-panel {') !== false, 'Panel surface contract is missing.');
panelExpect(strpos($panelCss, '.sx-panel--responsive') !== false, 'Responsive panel contract is missing.');
panelExpect(strpos($modelView, 'BackendPanelAsset::register($this)') !== false, 'Default model card does not register its panel asset.');

echo "Backend panel asset contract: OK\n";
