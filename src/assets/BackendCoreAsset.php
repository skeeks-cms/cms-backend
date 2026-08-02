<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;
use skeeks\sx\assets\Custom;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\YiiAsset;

/**
 * Functional runtime shared by every backend shell.
 *
 * This bundle intentionally owns no product theme and no legacy icon font.
 * Visual tokens belong to BackendThemeAsset; temporary Unify adapters and
 * Font Awesome compatibility are registered by their compatibility bundles.
 */
class BackendCoreAsset extends AssetBundle
{
    public $depends = [
        YiiAsset::class,
        Custom::class,
        BootstrapPluginAsset::class,
        BackendBlockerAsset::class,
        BackendNotifyAsset::class,
    ];
}
