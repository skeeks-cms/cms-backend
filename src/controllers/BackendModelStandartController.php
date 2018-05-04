<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\controllers;

use skeeks\cms\backend\actions\BackendGridModelAction;
use skeeks\cms\backend\actions\BackendModelDeleteAction;
use skeeks\cms\backend\actions\BackendModelUpdateAction;
use skeeks\cms\backend\BackendInfoInterface;
use skeeks\cms\backend\BackendModelControllerInterface;
use skeeks\cms\backend\BackendPermissionsInterface;
use skeeks\cms\backend\BackendUrlInterface;
use skeeks\cms\backend\HasModelInterface;
use yii\helpers\ArrayHelper;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class BackendModelStandartController extends BackendModelController
{
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => BackendGridModelAction::class,
            ],

            "update" => [
                'class' => BackendModelUpdateAction::class,
            ],
            "delete" => [
                'class' => BackendModelDeleteAction::class,
            ],
        ]);
    }
}