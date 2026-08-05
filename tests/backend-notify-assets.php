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

use skeeks\cms\backend\assets\BackendCoreAsset;
use skeeks\cms\backend\assets\BackendNotifyAsset;
use skeeks\sx\assets\ComponentNotifyJgrowl;
use skeeks\sx\assets\ComponentNotifyToast;
use skeeks\sx\assets\JqueryJgrowl;

Yii::setAlias('@skeeks/cms/backend', dirname(__DIR__).'/src');
Yii::setAlias('@skeeks/sx/assets', dirname(__DIR__, 2).'/yii2-sx/src/assets');

function notifyExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$visited = [];
$queue = [BackendCoreAsset::class];
while ($queue) {
    $class = array_shift($queue);
    if (isset($visited[$class])) {
        continue;
    }

    $visited[$class] = true;
    $asset = (new ReflectionClass($class))->newInstanceWithoutConstructor();
    foreach ((array)$asset->depends as $dependency) {
        if (class_exists($dependency)) {
            $queue[] = $dependency;
        }
    }
}

notifyExpect(isset($visited[BackendNotifyAsset::class]), 'Backend notify theme asset is missing.');
notifyExpect(isset($visited[ComponentNotifyToast::class]), 'Native toast runtime is missing.');
notifyExpect(!isset($visited[ComponentNotifyJgrowl::class]), 'Legacy JGrowl notify component leaked into the standard graph.');
notifyExpect(!isset($visited[JqueryJgrowl::class]), 'JGrowl assets leaked into the standard graph.');

$backendApp = file_get_contents(dirname(__DIR__).'/src/assets/src/backend-app.js');
$shellCss = file_get_contents(dirname(__DIR__).'/src/assets/src/shell.css');
$notifyBase = file_get_contents(dirname(__DIR__, 2).'/yii2-sx/src/assets/js/components/notify/Notify.js');
$notifyToast = file_get_contents(dirname(__DIR__, 2).'/yii2-sx/src/assets/js/components/notify/NotifyToast.js');
notifyExpect(strpos($backendApp, 'jGrowl') === false, 'backend-app.js still configures JGrowl.');
notifyExpect(strpos($shellCss, 'jGrowl') === false, 'shell.css still carries JGrowl presentation.');

foreach (['defaul', 'notice', 'info', 'success', 'warning', 'error', 'fail'] as $method) {
    notifyExpect(
        strpos($notifyBase, $method.': function') !== false,
        'Compatible sx.notify.'.$method.' method is missing.'
    );
}
notifyExpect(strpos($notifyToast, 'sx.notify.show = function') !== false, 'Dynamic sx.notify.show API is missing.');
notifyExpect(strpos($notifyToast, 'sx.notify.clear = function') !== false, 'sx.notify.clear API is missing.');
notifyExpect(strpos($notifyToast, 'allowHtml') !== false, 'Safe text rendering option is missing.');

echo "Backend notification asset contract: OK\n";
