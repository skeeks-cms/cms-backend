<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets\assets;
use yii\web\AssetBundle;

/**
 * Class ControllerActionsWidgetAsset
 *
 * @package skeeks\cms\backend\widgets
 */
class ControllerActionsWidgetAsset extends AssetBundle
{

    public $sourcePath = '@skeeks/cms/backend/widgets/assets/src';

    public $css = [
    ];
    public $js = [
        'js/controller-actions-widget.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        '\skeeks\sx\assets\Custom',
    ];
}
