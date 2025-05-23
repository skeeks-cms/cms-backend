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
class BackendSearchAndFiltersWidgetAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = "@skeeks/cms/backend/widgets/assets/src";

    /**
     * @var array
     */
    public $css = [
        'css/backend-search-and-filters.css',
    ];

    public $js = [
        //'js/classes/Form.js',
    ];

    public $depends = [
        //'skeeks\cms\admin\assets\AdminAsset',
        'skeeks\sx\assets\Core',
    ];
}

