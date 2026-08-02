<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

use skeeks\cms\base\AssetBundle;

/**
 * Shared presentation for worker task calendars.
 *
 * Domain packages own their queries and markup while this asset owns the
 * common calendar geometry and semantic palette.
 */
class WorkerTasksCalendarAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/backend/assets/src';

    public $css = [
        'worker-tasks-calendar.css',
    ];

    public $depends = [
        BackendUiAsset::class,
    ];
}
