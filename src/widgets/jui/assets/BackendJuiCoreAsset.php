<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets\jui\assets;

use yii\web\AssetBundle;

/**
 * Minimal jQuery UI namespace bootstrap for selective widget bundles.
 */
class BackendJuiCoreAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/widgets/jui/assets/src';

    public $js = [
        'core.js',
    ];
}
