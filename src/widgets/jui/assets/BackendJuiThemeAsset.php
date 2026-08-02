<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets\jui\assets;

use yii\web\AssetBundle;

/**
 * Compatibility theme for existing jQuery UI sortable consumers.
 */
class BackendJuiThemeAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-ui';

    public $css = [
        'themes/smoothness/jquery-ui.min.css',
    ];
}
