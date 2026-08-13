<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets\sortable\assets;

use skeeks\cms\base\AssetBundle;
use skeeks\sx\assets\Core;

/**
 * Stable backend API for sortable collections.
 *
 * The provider dependency can be replaced without changing consumers.
 */
class BackendSortableAdapterAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/widgets/sortable/assets/src';

    public $js = [
        'sortable-adapter.js',
    ];

    public $css = [
        'sortable.css',
    ];

    public $depends = [
        Core::class,
        BackendSortableJsAsset::class,
    ];
}
