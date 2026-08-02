<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use yii\web\YiiAsset;

/**
 * Popper provider for Bootstrap dropdowns and tooltips.
 */
class BackendPopperAsset extends \skeeks\cms\base\AssetBundle
{
    public $sourcePath = '@skeeks/assets/unify/template/html/';

    public $js = [
        'assets/vendor/popper.js/popper.min.js',
    ];

    public $depends = [
        YiiAsset::class,
    ];
}
