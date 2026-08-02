<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets\jui\assets;

use skeeks\cms\base\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Selective jQuery UI modules required by Sortable.
 */
class BackendSortableAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-ui';

    public $js = [
        'ui/minified/data.js',
        'ui/minified/disable-selection.js',
        'ui/minified/focusable.js',
        'ui/minified/form.js',
        'ui/minified/ie.js',
        'ui/minified/keycode.js',
        'ui/minified/labels.js',
        'ui/minified/plugin.js',
        'ui/minified/safe-active-element.js',
        'ui/minified/safe-blur.js',
        'ui/minified/scroll-parent.js',
        'ui/minified/tabbable.js',
        'ui/minified/unique-id.js',
        'ui/minified/version.js',
        'ui/minified/widget.js',
        'ui/widgets/mouse.js',
        'ui/widgets/sortable.js',
    ];

    public $depends = [
        JqueryAsset::class,
        BackendJuiCoreAsset::class,
        BackendJuiThemeAsset::class,
    ];
}
