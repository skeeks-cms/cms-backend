<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets\assets;

use yii\web\AssetBundle;
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class FormButtonsBackendWidgetAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/widgets/assets/src';

    public $css = [
    ];
    public $js = [
        'js/form-buttons.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'skeeks\sx\assets\Custom',
    ];
}