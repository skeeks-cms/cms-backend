<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;
use skeeks\sx\assets\ComponentNotifyToast;

/**
 * Semantic toast notifications shared by admin, UPA and future cabinets.
 *
 * The public JavaScript contract remains sx.notify.*. This asset maps the
 * dependency-free runtime to backend theme tokens and shell geometry.
 */
class BackendNotifyAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'notify.css',
    ];

    public $depends = [
        BackendThemeAsset::class,
        ComponentNotifyToast::class,
    ];
}
