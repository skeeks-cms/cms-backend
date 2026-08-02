<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Shared backend theme foundation.
 *
 * Contains light/dark tokens and the smallest semantic control primitives.
 * Projects should override the variables exposed by this asset instead of
 * copying component rules.
 */
class BackendThemeAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'theme.css',
    ];

    public $js = [
        'theme-mode.js',
    ];
}
