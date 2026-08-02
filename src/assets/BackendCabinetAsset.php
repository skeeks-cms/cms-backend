<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Reference customer-cabinet presentation over the shared backend runtime.
 *
 * Projects customize this contract primarily through semantic variables and
 * load additional CSS only for genuinely product-specific screens.
 */
class BackendCabinetAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'cabinet.css',
    ];

    public $depends = [
        BackendAppAsset::class,
    ];
}
