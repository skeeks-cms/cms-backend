<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Reusable backend header shell.
 */
class BackendShellHeaderAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'shell-header.css',
    ];

    public $js = [
        'shell-header.js',
    ];

    public $depends = [
        BackendShellAsset::class,
    ];
}
