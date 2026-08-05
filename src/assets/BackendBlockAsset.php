<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Compatibility styles for historical sx-block markup.
 *
 * @deprecated New interfaces must use BackendSurfaceWidget or sx-surface.
 */
class BackendBlockAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'block.css',
    ];

    public $depends = [
        BackendUiAsset::class,
    ];
}
