<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Shared same-origin iframe communication used by backend widgets.
 *
 * The runtime is structural and has no dependency on an admin theme.
 */
class BackendIframeAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $js = [
        'backend-iframe.js',
    ];

    public $depends = [
        BackendCoreAsset::class,
    ];
}
