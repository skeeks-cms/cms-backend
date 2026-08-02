<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

/**
 * Backend-owned jQuery provider with the migrate bridge required by old
 * controller scripts.
 */
class BackendJqueryAsset extends \yii\web\JqueryAsset
{
    public $sourcePath = '@skeeks/cms/backend/assets/src/vendor/jquery';

    public $js = [
        'jquery-3.6.0.min.js',
        'jquery-migrate-3.4.0.min.js',
    ];

    public $depends = [];
}
