<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Standard application runtime for backend shells and customer cabinets.
 *
 * Product data and endpoints remain opt-in through window.sxQuickAccessData.
 * Theme packages may add visual adapters but must not duplicate this behavior.
 */
class BackendAppAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $js = [
        'backend-blocker.js',
        'backend-app.js',
    ];

    public $depends = [
        BackendShellAsset::class,
        BackendWindowCompatibilityAsset::class,
    ];
}
