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
use skeeks\cms\backend\widgets\BackendSurfaceWidget;

Yii::setAlias('@skeeks/cms/backend', dirname(__DIR__).'/src');

function panelExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$uiAsset = (new ReflectionClass(BackendUiAsset::class))->newInstanceWithoutConstructor();
$panelAsset = (new ReflectionClass(BackendPanelAsset::class))->newInstanceWithoutConstructor();

panelExpect(!in_array(BackendPanelAsset::class, (array)$uiAsset->depends, true), 'Compatibility panel asset leaked into BackendUiAsset.');
panelExpect(in_array(BackendUiAsset::class, (array)$panelAsset->depends, true), 'Compatibility panel asset does not build on BackendUiAsset.');
panelExpect($panelAsset->css === ['panel.css'], 'Compatibility panel asset must keep publishing panel.css.');

$themeCss = file_get_contents(dirname(__DIR__).'/src/assets/src/theme.css');
$uiCss = file_get_contents(dirname(__DIR__).'/src/assets/src/ui.css');
$backendCss = file_get_contents(dirname(__DIR__).'/src/assets/src/backend.css');
$panelCss = file_get_contents(dirname(__DIR__).'/src/assets/src/panel.css');
$modelView = file_get_contents(dirname(__DIR__).'/src/actions/views/model-view.php');
$surfaceWidget = file_get_contents(dirname(__DIR__).'/src/widgets/BackendSurfaceWidget.php');

panelExpect(strpos($themeCss, '.sx-surface__header') !== false, 'Canonical surface structure is missing from the global theme.');
panelExpect(strpos($themeCss, '.sx-surface--responsive') !== false, 'Responsive surface contract is missing.');
foreach ([$themeCss, $uiCss, $backendCss] as $globalCss) {
    panelExpect(strpos($globalCss, 'sx-panel') === false, 'Deprecated sx-panel contract leaked into global backend CSS.');
}
panelExpect(strpos($panelCss, '.sx-panel {') !== false, 'Legacy panel contract is missing.');
panelExpect(strpos($panelCss, '.sx-panel--responsive') !== false, 'Responsive panel compatibility is missing.');
panelExpect(class_exists(BackendSurfaceWidget::class), 'Canonical surface widget is not autoloadable.');
panelExpect(strpos($surfaceWidget, "Html::addCssClass(\$this->options, 'sx-surface')") !== false, 'Surface widget does not emit the canonical root class.');
panelExpect(strpos($surfaceWidget, 'BackendUiAsset::register($this->getView())') !== false, 'Surface widget does not register BackendUiAsset.');
panelExpect(strpos($modelView, 'BackendSurfaceWidget::widget') !== false, 'Default model card does not use the canonical surface widget.');
panelExpect(strpos($modelView, 'BackendPanelAsset') === false, 'Default model card still registers the compatibility panel asset.');

echo "Backend surface compatibility contract: OK\n";
