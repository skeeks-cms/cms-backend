<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

/**
 * Bootstrap 4 CSS provider used by the current backend compatibility layer.
 *
 * The source package is temporary infrastructure; semantic UI must not use
 * Unify selectors or treat this file as a theme.
 */
class BackendBootstrapAsset extends \yii\bootstrap\BootstrapAsset
{
    public $sourcePath = '@skeeks/assets/unify/template/html/';

    public $css = [
        'assets/vendor/bootstrap/bootstrap.min.css',
    ];
}
