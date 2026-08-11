<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets\sortable\assets;

use yii\web\AssetBundle;

/**
 * Lightweight SortableJS provider for the backend sortable adapter.
 */
class BackendSortableJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/sortablejs';

    public $js = [
        'Sortable.min.js',
    ];
}
