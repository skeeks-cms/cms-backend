<?php

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Makes the dependency-light backend drawer the standard sx window.
 *
 * Register this only in shells that have completed the backend-window
 * compatibility migration. BackendWindowAsset itself remains opt-in and
 * does not replace sx.classes.Window.
 */
class BackendWindowCompatibilityAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $js = [
        'backend-window-compatibility.js',
    ];

    public $depends = [
        BackendWindowAsset::class,
    ];
}
