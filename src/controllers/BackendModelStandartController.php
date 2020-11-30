<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\controllers;

use skeeks\cms\backend\actions\BackendGridModelAction;
use skeeks\cms\backend\actions\BackendModelCreateAction;
use skeeks\cms\backend\actions\BackendModelDeleteAction;
use skeeks\cms\backend\actions\BackendModelMultiDeleteAction;
use skeeks\cms\backend\actions\BackendModelUpdateAction;
use skeeks\cms\backend\BackendInfoInterface;
use skeeks\cms\backend\BackendModelControllerInterface;
use skeeks\cms\backend\BackendPermissionsInterface;
use skeeks\cms\backend\BackendUrlInterface;
use skeeks\cms\backend\HasModelInterface;
use skeeks\cms\helpers\RequestResponse;
use yii\helpers\ArrayHelper;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class BackendModelStandartController extends BackendModelController
{
    public function actions()
    {
        $actions = ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => BackendGridModelAction::class,
                'accessCallback'  => true,
            ],

            "create" => [
                'class' => BackendModelCreateAction::class,
            ],

            "update" => [
                'class'    => BackendModelUpdateAction::class,
                'priority' => 100,
            ],
            "delete" => [
                'class'    => BackendModelDeleteAction::class,
                'priority' => 9999,
            ],

            "delete-multi" => [
                'class' => BackendModelMultiDeleteAction::class,
                "eachAccessCallback" => function($model) {
                    return \Yii::$app->user->can($this->permissionName . "/delete", ['model' => $model]);
                },
                "accessCallback" => function() {
            print_r($this->permissionName);die;
                    return \Yii::$app->user->can($this->permissionName . "/delete");
                },
            ],
        ]);

        return $actions;
    }
}