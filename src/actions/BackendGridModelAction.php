<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\actions\assets\BackendGridModelActionAsset;
use skeeks\cms\backend\BackendAction;
use skeeks\cms\backend\BackendComponent;
use skeeks\cms\backend\grid\ControllerActionsColumn;
use skeeks\cms\backend\models\BackendShowing;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use skeeks\cms\helpers\StringHelper;
use skeeks\cms\widgets\DynamicFiltersWidget;
use skeeks\cms\widgets\FiltersWidget;
use skeeks\yii2\config\storages\ConfigDbModelStorage;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
/**
 * @property string $gridClassName
 * @property [] $gridConfig
 *
 * ***
 *
 * Class BackendGridModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendGridModelAction extends BackendAction
{
    /**
     * @var
     */
    public $grid;

    /**
     * @var FiltersWidget
     */
    public $filters;

    public $backendShowingParam = 'sx-backend-showing';
    /**
     * @var
     */
    protected $_modelClassName;
    protected $_initMultiOptions = null;
    protected $_buttonsMulti = null;
    protected $_additionalsMulti = null;
    /**
     * @var BackendShowing
     */
    protected $_backendShowing = null;
    /**
     * @return string
     */
    public function getModelClassName()
    {
        return (string)$this->controller->modelClassName;
    }

    public function init()
    {
        if (!$this->icon) {
            $this->icon = "glyphicon glyphicon-th-list";
        }


        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', "List");
        }


        $r = new \ReflectionClass($this->backendShowing);
        $backendShowingId = $this->backendShowing->id;
        $backendShowingClassName = $r->getName();

        $defaultGrid = [
            'class'              => GridViewWidget::class,
            'beforeTableRight'   => function (GridViewWidget $gridViewWidget) {

                $id = \Yii::$app->controller->action->backendShowing->id;
                $editComponent = [
                    'url' => \skeeks\cms\backend\helpers\BackendUrlHelper::createByParams([
                        BackendComponent::getCurrent()->backendShowingControllerRoute.'/component-call-edit',
                    ])
                        ->merge([
                            'id'                 => $id,
                            'componentClassName' => $gridViewWidget::className(),
                            'callable_id'        => $gridViewWidget->id."-edit",
                        ])
                        ->enableEmptyLayout()
                        ->enableNoActions()
                        ->url,
                ];
                $editComponent = Json::encode($editComponent);
                $callableDataInput = Html::textarea('callableData', base64_encode(serialize($gridViewWidget->editData)), [
                    'id'    => $gridViewWidget->id."-edit",
                    'style' => 'display: none;',
                ]);
                return '<div class="sx-grid-settings">'.Html::a('<i class="glyphicon glyphicon-cog"></i>',
                        '#', [
                            'class'   => 'btn btn-sm',
                            'onclick' => new JsExpression(<<<JS
            new sx.classes.backend.EditComponent({$editComponent}); return false;
JS
                            ),
                        ]).$callableDataInput."</div>";
            },
            'modelClassName'     => $this->modelClassName,
            'configBehaviorData' => [
                'configKey'     => $this->uniqueId,
                'configStorage' => [
                    'class'          => ConfigDbModelStorage::class,
                    'modelClassName' => $backendShowingClassName,
                    'primaryKey'     => $backendShowingId,
                    'attribute'      => 'config_jsoned',
                ],
            ],
            'columns'            => [
                'serial'   => [
                    'class'   => 'yii\grid\SerialColumn',
                    'visible' => false,
                ],
                'checkbox' => [
                    'class'         => 'skeeks\cms\grid\CheckboxColumn',
                    'headerOptions' => [
                        'class' => 'sx-grid-checkbox',
                    ],
                ],
                'actions'  => [
                    'class'           => ControllerActionsColumn::class,
                    'controller'      => function ($action) {
                        return $this->controller;
                    },
                    'label'           => \Yii::t('skeeks/backend', 'Actions'),
                    'headerOptions'   => [
                        'class' => 'sx-grid-actions',
                    ],
                ],

            ],
        ];

        parent::init();

        $defaultFilters = [
            'class'              => \skeeks\cms\backend\widgets\FiltersWidget::class,
            'activeForm'         => [
                'action' => $this->getShowingUrl($this->getBackendShowing())
            ],
            'configBehaviorData' => [
                'configKey'     => $this->uniqueId,
                'configStorage' => [
                    'class'          => ConfigDbModelStorage::class,
                    'modelClassName' => $backendShowingClassName,
                    'primaryKey'     => $backendShowingId,
                    'attribute'      => 'config_jsoned',
                ],
            ],
        ];

        $this->grid = (array)ArrayHelper::merge($defaultGrid, (array)$this->grid);
        $this->filters = (array)ArrayHelper::merge($defaultFilters, (array)$this->filters);

        BackendGridModelActionAsset::register(\Yii::$app->view);

    }

    public function getFiltersConfig()
    {
        $filters = $this->filters;
        ArrayHelper::remove($filters, 'class');
        return (array)$filters;
    }

    public function getFiltersClassName()
    {
        return (string)ArrayHelper::getValue($this->filters, 'class');
    }

    public function getGridClassName()
    {
        return (string)ArrayHelper::getValue($this->grid, 'class');
    }

    /**
     * @return string
     */
    public function getGridConfig()
    {
        $grid = $this->grid;
        ArrayHelper::remove($grid, 'class');
        return (array)$grid;
    }

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        return $this->controller->render('@skeeks/cms/backend/actions/views/grid');
    }
    /**
     * @return string
     */
    public function renderBeforeTable()
    {
        $multiActions = [];
        if ($this->controller) {
            $multiActions = $this->controller->modelMultiActions;
        }

        if (!$multiActions) {
            return parent::renderBeforeTable();
        }

        $this->_initMultiActions();
        $this->beforeTableLeft = $this->_buttonsMulti;

        return parent::renderBeforeTable();
    }

    protected function _initMultiActions()
    {
        if ($this->_initMultiOptions === true) {
            return $this;
        }

        $this->_initMultiOptions = true;

        $multiActions = [];
        if ($this->adminController) {
            $multiActions = $this->adminController->modelMultiActions;
        }

        if (!$multiActions) {
            return $this;
        }

        $options = [
            'id'                 => $this->id,
            'requestPkParamName' => $this->controller->requestPkParamName,
        ];
        $optionsString = Json::encode($options);

        $gridJsObject = $this->getGridJsObject();

        $this->view->registerJs(<<<JS
        {$gridJsObject} = new sx.classes.grid.Standart($optionsString);
JS
        );

        $buttons = "";

        $additional = [];
        foreach ($multiActions as $action) {
            $additional[] = $action->registerForGrid($this);

            $buttons .= <<<HTML
            <button class="btn btn-default btn-sm sx-grid-multi-btn" data-id="{$action->id}">
                <i class="{$action->icon}"></i> {$action->name}
            </button>
HTML;
        }

        $additional = implode("", $additional);

        $checkbox = Html::checkbox('sx-select-full-all', false, [
            'class' => 'sx-select-full-all',
        ]);

        $this->_buttonsMulti = <<<HTML
    {$checkbox} для всех
    <span class="sx-grid-multi-controlls">
        {$buttons}
    </span>
HTML;
        $this->_additionalsMulti = $additional;

        $this->view->registerCss(<<<CSS
    .sx-grid-multi-controlls
    {
        margin-left: 20px;
    }
CSS
        );
    }
    /**
     * @return string
     */
    public function renderAfterTable()
    {
        $multiActions = [];
        if ($this->adminController) {
            $multiActions = $this->adminController->modelMultiActions;
        }

        if (!$multiActions) {
            return parent::renderAfterTable();
        }

        $this->_initMultiActions();
        $this->afterTableLeft = $this->_buttonsMulti.$this->_additionalsMulti;

        return parent::renderAfterTable();
    }


    public function getBackendShowing()
    {
        if ($this->_backendShowing === null || !$this->_backendShowing instanceof BackendShowing) {
            //Find in get params
            if ($id = (int)\Yii::$app->request->get($this->backendShowingParam)) {
                if ($backendShowing = BackendShowing::findOne($id)) {
                    $this->_backendShowing = $backendShowing;
                    return $this->_backendShowing;
                } /*else {
                    \Yii::$app->response->redirect($this->indexUrl);
                    \Yii::$app->end();
                }*/
            } elseif ($id = (int)\Yii::$app->request->post($this->backendShowingParam)) {
                if ($backendShowing = BackendShowing::findOne($id)) {
                    $this->_backendShowing = $backendShowing;
                    return $this->_backendShowing;
                }
            }

            //Defauilt filter
            $backendShowing = BackendShowing::find()
                ->where(['key' => $this->uniqueId])
                //->andWhere(['cms_user_id' => \Yii::$app->user->id])
                ->andWhere(['is_default' => 1])
                ->one();

            if (!$backendShowing) {
                $backendShowing = new BackendShowing([
                    'key'        => $this->uniqueId,
                    //'cms_user_id' => \Yii::$app->user->id,
                    'is_default' => 1,
                ]);
                $backendShowing->loadDefaultValues();

                if ($backendShowing->save()) {

                } else {
                    throw new Exception('Backend showing not saved');
                }
            }

            $this->_backendShowing = $backendShowing;
        }

        return $this->_backendShowing;
    }


    /**
     * @param BackendShowing $backendShowing
     * @return string
     */
    public function getShowingUrl(BackendShowing $backendShowing)
    {
        $query = [];
        $url = $this->url;

        if ($pos = strpos($url, "?")) {
            $url = StringHelper::substr($url, 0, $pos);
            $stringQuery = StringHelper::substr($url, $pos + 1, StringHelper::strlen($url));
            parse_str($stringQuery, $query);
        }

        $query = [];
        /*if ($filter->values)
        {
            $query = (array) $filter->values;
        }*/

        $query[$this->backendShowingParam] = $backendShowing->id;
        return $url."?".http_build_query($query);
    }

    /**
     * @return array|BackendShowing[]
     */
    public function getBackendShowings()
    {
        return BackendShowing::find()->where([
            'key' => $this->uniqueId,
        ])
            ->andWhere([
                'or',
                ['cms_user_id' => null],
                ['cms_user_id' => \Yii::$app->user->id],
            ])
            ->orderBy(['priority' => SORT_ASC])
            ->all();
    }
}