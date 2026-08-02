<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Reusable backend sidebar/menu shell.
 */
class BackendShellMenuAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'shell-menu.css',
    ];

    public $js = [
        'shell-menu.js',
    ];

    public $depends = [
        BackendShellAsset::class,
    ];
}
