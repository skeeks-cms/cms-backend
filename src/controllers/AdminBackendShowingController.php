<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 17.11.2015
 */

namespace skeeks\cms\backend\controllers;

use skeeks\cms\backend\actions\BackendGridModelAction;
use skeeks\cms\backend\actions\BackendModelCreateAction;
use skeeks\cms\backend\actions\BackendModelDeleteAction;
use skeeks\cms\backend\actions\BackendModelUpdateAction;
use skeeks\cms\backend\controllers\BackendModelController;
use skeeks\cms\backend\models\BackendShowing;
use skeeks\cms\models\CmsAgent;
use skeeks\hosting\models\HostingCreateVps;
use skeeks\sx\helpers\ResponseHelper;
use yii\helpers\ArrayHelper;

/**
 * Class AdminVpsController
 * @package skeeks\hosting\controllers
 */
class AdminBackendShowingController extends BackendModelController
{
    public function init()
    {
        $this->name = \Yii::t('skeeks/backend', 'Showings');
        $this->modelShowAttribute = "name";
        $this->modelClassName = BackendShowing::class;

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [

                "create" => [
                    'class'         => BackendModelCreateAction::class,
                    'callback' => [$this, 'actionCreate']
                ],
                "update" => [
                    'class'         => BackendModelUpdateAction::class,
                    'fields' => [
                        'name',
                    ]
                ],
                "delete" => [
                    'class'         => BackendModelDeleteAction::class
                ],


                'index' => [
                    "name"    => \Yii::t('skeeks/backend', 'Showings'),
                    'class'   => BackendGridModelAction::class,
                    "filters" => [
                        /*'visibleFilters' => [
                            'id',
                            'created_by',
                        ],*/
                        /*'filtersModel'   => [
                            'rules'            => [
                                ['test', 'safe'],
                            ],
                            'attributeDefines' => [
                                'test',
                            ],
                            'fields'           => [
                                'test' => [
                                ],
                            ],
                        ],*/
                    ],
                    "grid"    => [
                        /*'on ready' => function(Event $e) {
                            /**
                             * @var $dataProvider ActiveDataProvider
                            $dataProvider = $e->sender->dataProvider;
                            $dataProvider->query->andWhere(['cms_user_id' => \Yii::$app->user->id]);
                        },*/
                        /*'visibleColumns' => [
                            'checkbox',
                            'actions',
                            'id',
                            'status',

                            'cms_user_id',
                            'hosting_vps_tariff_id',
                        ],

                        'columns' => [
                            'status' => [
                                'value' => function (HostingVps $model) {
                                    return $model->statusText;
                                },
                            ],
                        ],*/
                    ],
                ],

            ]
        );
    }

    public function actionCreate()
    {
        $rr = new ResponseHelper();
        $showing = new BackendShowing();

        if (\Yii::$app->request->post()) {
            if (\Yii::$app->request->get('sx-validate')) {
                return $rr->ajaxValidateForm($showing);
            } else {
                if ($showing->load(\Yii::$app->request->post()) && $showing->save()) {
                    $rr->success = true;
                }
            }
        }

        return $rr;
    }

}
