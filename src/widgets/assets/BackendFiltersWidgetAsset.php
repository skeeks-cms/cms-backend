<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets\assets;

use skeeks\cms\base\AssetBundle;
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class BackendFiltersWidgetAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = "@skeeks/cms/backend/widgets/assets/src";

    /**
     * @var array
     */
    public $css = [
        'css/backend-filter-theme.css',
        'css/backend-filters.css',
    ];

    public $js = [
        //'js/classes/Form.js',
    ];

    public $depends = [
        BackendFormAsset::class,
        'skeeks\sx\assets\Core',
    ];
}
