<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 21.08.2017
 */
namespace skeeks\cms\backend\widgets\assets;
use yii\web\AssetBundle;
/**
 * Class SelectModelDialogWidgetAsset
 *
 * @package skeeks\widget\SelectModelDialog\assets
 */
class SelectModelDialogWidgetAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/widgets/assets/src';
    public $css = [
        'css/select-model-dialog.css',
    ];
    public $js =
    [
        'js/select-model-dialog.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'skeeks\sx\assets\Custom',
    ];
}