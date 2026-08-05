<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Optional compatibility asset for historical sx-panel consumers.
 *
 * New interfaces use BackendSurfaceWidget and the global sx-surface contract.
 * Keep this bundle functional while installed views still emit sx-panel.
 */
class BackendPanelAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'panel.css',
    ];

    public $depends = [
        BackendUiAsset::class,
    ];
}
