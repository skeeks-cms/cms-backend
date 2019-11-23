<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 21.08.2017
 */
namespace skeeks\cms\backend\widgets\assets;
use skeeks\cms\base\AssetBundle;
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class AjaxControllerActionsWidgetAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/widgets/assets/src';
    
    public $css = [
        'css/ajax-controller-actions-widget.css',
    ];
    
    public $js = [
        'js/ajax-controller-actions-widget.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'skeeks\sx\assets\Custom',
    ];
}