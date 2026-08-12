<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\actions\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Base grid multi-action runtime. Registered only when a grid has multi-actions.
 */
class BackendGridModelMultiActionAsset extends AssetBundle
{
    public $depends = [
        BackendGridModelActionAsset::class,
        'skeeks\cms\modules\admin\widgets\gridViewStandart\GridViewStandartAsset',
    ];
}
