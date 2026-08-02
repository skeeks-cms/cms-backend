<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Reusable backend and customer-cabinet shell.
 *
 * Contains common content geometry and legacy-safe shell adapters. Brand
 * values belong to project variables; Unify utilities belong to the temporary
 * compatibility provider.
 */
class BackendShellAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'shell.css',
    ];

    public $depends = [
        BackendUiAsset::class,
    ];
}
