<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 27.04.2016
 */
namespace skeeks\cms\backend\actions\assets;

use skeeks\cms\base\AssetBundle;
use yii\bootstrap\BootstrapAsset;

/**
 * Class SelectLanguage
 * @package common\widgets\selectLanguage
 */
class BackendGridModelActionAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/actions/assets/src';

    public $css = [
        'filters.css',
    ];

    public $js = [
        'filters.js',
    ];

    public $depends =
    [
        'yii\web\YiiAsset',
        'skeeks\sx\assets\Custom',
        'skeeks\sx\assets\ComponentAjaxLoader',
        'skeeks\cms\base\AssetBundle',
    ];
}