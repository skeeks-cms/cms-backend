<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\JqueryAsset;

/**
 * Bootstrap 4 behavior provider shared by administration and cabinets.
 */
class BackendBootstrapPluginAsset extends \yii\bootstrap\BootstrapPluginAsset
{
    public $sourcePath = '@skeeks/assets/unify/template/html/';

    public $js = [
        'assets/vendor/bootstrap/bootstrap.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
        BackendPopperAsset::class,
    ];
}
