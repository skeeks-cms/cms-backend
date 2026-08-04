<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Shared live theme editor for backend, UPA and future cabinet shells.
 */
class BackendThemeCustomizerAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'theme-customizer.css',
    ];

    public $js = [
        'theme-customizer.js',
    ];

    public $depends = [
        BackendThemeAsset::class,
        BackendNotifyAsset::class,
    ];
}
