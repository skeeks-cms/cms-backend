<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Optional semantic panel/card component.
 *
 * Keep this outside BackendUiAsset: tables, forms and empty backend layouts do
 * not need panel structure. Consumers that emit `.sx-panel` register it.
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
