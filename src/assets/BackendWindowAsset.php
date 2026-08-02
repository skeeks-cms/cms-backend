<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;
use skeeks\sx\assets\Custom;
use yii\web\YiiAsset;

/**
 * Dependency-light iframe drawer used by backend actions.
 *
 * It is intentionally independent from Fancybox so the backend shell can
 * migrate action windows without coupling ordinary pages to a gallery plugin.
 */
class BackendWindowAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'backend-window.css',
    ];

    public $js = [
        'backend-window.js',
    ];

    public $depends = [
        YiiAsset::class,
        Custom::class,
        BackendUiAsset::class,
    ];
}
