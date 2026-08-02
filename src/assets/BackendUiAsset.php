<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;
/**
 * Shared semantic UI components for backend controllers and customer cabinets.
 *
 * This remains the compatibility entry point for consumers that need the full
 * semantic UI contract. BackendThemeAsset is loaded first through dependency.
 */
class BackendUiAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'ui.css',
    ];

    public $depends = [
        BackendCoreAsset::class,
        BackendThemeAsset::class,
    ];
}
