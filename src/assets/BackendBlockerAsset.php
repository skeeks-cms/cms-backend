<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;
use skeeks\sx\assets\Custom;

/**
 * Semantic container-blocking tokens shared by admin, UPA and future cabinets.
 *
 * Runtime behavior and the public sx.block API belong to skeeks/yii2-sx.
 */
class BackendBlockerAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'blocker.css',
    ];

    public $depends = [
        BackendThemeAsset::class,
        Custom::class,
    ];
}
