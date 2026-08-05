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

use skeeks\cms\backend\assets\BackendBlockAsset;
use skeeks\cms\backend\assets\BackendUiAsset;

Yii::setAlias('@skeeks/cms/backend', dirname(__DIR__).'/src');

function blockExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$uiAsset = (new ReflectionClass(BackendUiAsset::class))->newInstanceWithoutConstructor();
$blockAsset = (new ReflectionClass(BackendBlockAsset::class))->newInstanceWithoutConstructor();

blockExpect(!in_array(BackendBlockAsset::class, (array)$uiAsset->depends, true), 'Deprecated block asset leaked into BackendUiAsset.');
blockExpect(in_array(BackendUiAsset::class, (array)$blockAsset->depends, true), 'Block asset does not build on BackendUiAsset.');
blockExpect($blockAsset->css === ['block.css'], 'Block asset must publish only block.css.');

$themeCss = file_get_contents(dirname(__DIR__).'/src/assets/src/theme.css');
$uiCss = file_get_contents(dirname(__DIR__).'/src/assets/src/ui.css');
$backendCss = file_get_contents(dirname(__DIR__).'/src/assets/src/backend.css');
$blockCss = file_get_contents(dirname(__DIR__).'/src/assets/src/block.css');
$modelLog = file_get_contents(dirname(__DIR__).'/src/actions/views/model-log.php');

foreach ([$themeCss, $uiCss, $backendCss] as $globalCss) {
    blockExpect(strpos($globalCss, 'sx-block') === false, 'Deprecated sx-block contract leaked into global backend CSS.');
}
blockExpect(strpos($blockCss, 'html[data-sx-theme] .sx-block {') !== false, 'Block surface compatibility is missing.');
blockExpect(strpos($blockCss, '.sx-block-title') !== false, 'Block content compatibility is missing.');
blockExpect(strpos($modelLog, 'BackendSurfaceWidget::widget') !== false, 'Generic model log does not use the canonical surface widget.');
blockExpect(strpos($modelLog, 'BackendBlockAsset') === false, 'Generic model log still registers the deprecated block asset.');
blockExpect(strpos($modelLog, 'sx-block') === false, 'Generic model log still emits deprecated block markup.');

echo "Backend block asset contract: OK\n";
