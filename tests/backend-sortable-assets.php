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

use skeeks\cms\backend\widgets\jui\assets\BackendJuiThemeAsset;
use skeeks\cms\backend\widgets\jui\assets\BackendSortableAsset;
use skeeks\cms\backend\widgets\sortable\assets\BackendSortableAdapterAsset;
use skeeks\cms\backend\widgets\sortable\assets\BackendSortableJsAsset;

Yii::setAlias('@skeeks/cms/backend', dirname(__DIR__).'/src');

function sortableAssetExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$sortableAsset = (new ReflectionClass(BackendSortableAsset::class))->newInstanceWithoutConstructor();
$adapterAsset = (new ReflectionClass(BackendSortableAdapterAsset::class))->newInstanceWithoutConstructor();
$themeAsset = (new ReflectionClass(BackendJuiThemeAsset::class))->newInstanceWithoutConstructor();
$sortableCss = file_get_contents(dirname(__DIR__).'/src/widgets/jui/assets/src/sortable.css');
$sortableAdapter = file_get_contents(dirname(__DIR__).'/src/widgets/sortable/assets/src/sortable-adapter.js');
$adapterCss = file_get_contents(dirname(__DIR__).'/src/widgets/sortable/assets/src/sortable.css');

sortableAssetExpect(
    in_array(BackendJuiThemeAsset::class, (array)$sortableAsset->depends, true),
    'Sortable asset does not register its minimal style contract.'
);
sortableAssetExpect(
    $themeAsset->sourcePath === '@skeeks/cms/backend/widgets/jui/assets/src',
    'Sortable styles are not published from the backend package.'
);
sortableAssetExpect($themeAsset->css === ['sortable.css'], 'Sortable asset publishes an unexpected stylesheet.');
sortableAssetExpect(
    strpos($sortableCss, '.ui-sortable-placeholder.ui-state-highlight') !== false,
    'Sortable placeholder styling is missing.'
);
sortableAssetExpect(
    strpos($sortableCss, 'var(--sx-color-accent-soft') !== false,
    'Sortable placeholder does not follow the active backend theme.'
);
sortableAssetExpect(strpos($sortableCss, 'url(') === false, 'Sortable styles still load theme images.');
sortableAssetExpect(
    in_array(BackendSortableJsAsset::class, (array)$adapterAsset->depends, true),
    'Sortable adapter does not declare SortableJS as its provider dependency.'
);
sortableAssetExpect($adapterAsset->js === ['sortable-adapter.js'], 'Sortable adapter publishes unexpected scripts.');
sortableAssetExpect($adapterAsset->css === ['sortable.css'], 'Sortable adapter publishes unexpected styles.');
sortableAssetExpect(
    strpos($adapterCss, '.ui-state-highlight') !== false,
    'SortableJS placeholder styling is missing.'
);
sortableAssetExpect(
    $sortableAsset->js === [
        'ui/minified/data.js',
        'ui/minified/ie.js',
        'ui/minified/scroll-parent.js',
        'ui/minified/version.js',
        'ui/minified/widget.js',
        'ui/widgets/mouse.js',
        'ui/widgets/sortable.js',
    ],
    'Legacy jQuery UI Sortable asset contains modules outside its dependency graph.'
);
sortableAssetExpect(
    strpos($sortableAdapter, 'sx.backend.sortable.create') !== false,
    'Sortable adapter factory is missing.'
);
sortableAssetExpect(
    strpos($sortableAdapter, 'engine = "sortablejs"') !== false
    && strpos($sortableAdapter, 'onUpdate') !== false
    && strpos($sortableAdapter, 'oldIndex') !== false,
    'Sortable adapter does not expose its normalized update contract.'
);

echo "Backend sortable asset contract: OK\n";
