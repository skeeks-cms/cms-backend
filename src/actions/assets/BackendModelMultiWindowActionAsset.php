<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\actions\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Iframe bridge for parameterized grid multi-actions.
 */
class BackendModelMultiWindowActionAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/actions/assets/src';

    public $js = [
        'multi-window-action.js',
    ];

    public $depends = [
        BackendGridModelMultiActionAsset::class,
    ];
}
