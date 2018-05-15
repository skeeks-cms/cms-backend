<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\actions\assets\BackendGridModelActionAsset;
use skeeks\cms\backend\BackendComponent;
use skeeks\cms\backend\grid\ControllerActionsColumn;
use skeeks\cms\backend\models\BackendShowing;
use skeeks\cms\backend\ViewBackendAction;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use skeeks\cms\modules\admin\widgets\gridViewStandart\GridViewStandartAsset;
use skeeks\cms\widgets\DynamicFiltersWidget;
use skeeks\cms\widgets\FiltersWidget;
use skeeks\yii2\config\storages\ConfigDbModelStorage;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
/**
 * @property string $gridClassName
 * @property string $configKey
 * @property [] $gridConfig
 *
 * ***
 *
 * Class BackendGridModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendGridModelAction extends ViewBackendAction
{
    /**
     * @var
     */
    public $grid;

    /**
     * @var FiltersWidget
     */
    public $filters;


    /**
     * @var
     */
    protected $_modelClassName;
    protected $_initMultiOptions = null;
    protected $_buttonsMulti = null;
    protected $_additionalsMulti = null;

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
            'beforeTableLeft'    => function (GridViewWidget $gridViewWidget) {
                return $this->renderBeforeTable($gridViewWidget);
            },
            'afterTableLeft'     => function (GridViewWidget $gridViewWidget) {
                return $this->renderAfterTable($gridViewWidget);
            },
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
                'configKey'     => $this->configKey,
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
                    'class'         => ControllerActionsColumn::class,
                    'controller'    => function ($action) {
                        return $this->controller;
                    },
                    'label'         => \Yii::t('skeeks/backend', 'Actions'),
                    'headerOptions' => [
                        'class' => 'sx-grid-actions',
                    ],
                ],

            ],
        ];

        parent::init();

        $defaultFilters = [
            'class'              => \skeeks\cms\backend\widgets\FiltersWidget::class,
            'activeForm'         => [
                'action' => $this->getShowingUrl($this->getBackendShowing()),
            ],
            'configBehaviorData' => [
                'configKey'     => $this->configKey,
                'configStorage' => [
                    'class'          => ConfigDbModelStorage::class,
                    'modelClassName' => $backendShowingClassName,
                    'primaryKey'     => $backendShowingId,
                    'attribute'      => 'config_jsoned',
                ],
            ],
        ];

        $this->grid = (array)ArrayHelper::merge($defaultGrid, (array)$this->grid);
        if ($this->filters === false) {
            $this->filters = false;
        } else {
            $this->filters = (array)ArrayHelper::merge($defaultFilters, (array) $this->filters);
        }


        BackendGridModelActionAsset::register(\Yii::$app->view);

    }
    /**
     * @return string
     */
    public function renderBeforeTable(GridViewWidget $gridViewWidget)
    {
        GridViewStandartAsset::register($gridViewWidget->view);
        $multiActions = [];
        if ($this->controller) {
            $multiActions = $this->controller->modelMultiActions;
        }

        $this->_initMultiActions($gridViewWidget);
        return $this->_buttonsMulti;
    }
    protected function _initMultiActions(GridViewWidget $gridViewWidget)
    {
        if ($this->_initMultiOptions === true) {
            return $this;
        }

        $this->_initMultiOptions = true;

        $multiActions = [];
        if ($this->controller) {
            $multiActions = $this->controller->modelMultiActions;
        }

        if (!$multiActions) {
            return $this;
        }

        $options = [
            'id'                 => $gridViewWidget->id,
            'requestPkParamName' => $this->controller->requestPkParamName,
        ];
        $optionsString = Json::encode($options);

        $gridJsObject = "sx.Grid".$gridViewWidget->id;

        $gridViewWidget->view->registerJs(<<<JS
        {$gridJsObject} = new sx.classes.grid.Standart($optionsString);
JS
        );

        $buttons = "";

        $additional = [];
        foreach ($multiActions as $action) {
            $additional[] = $action->registerForGrid($gridViewWidget);

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

        $gridViewWidget->view->registerCss(<<<CSS
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
    public function renderAfterTable(GridViewWidget $gridViewWidget)
    {
        $multiActions = [];
        if ($this->controller) {
            $multiActions = $this->controller->modelMultiActions;
        }


        $this->_initMultiActions($gridViewWidget);
        return $this->_buttonsMulti.$this->_additionalsMulti;

    }
    public function getFiltersConfig()
    {
        $filters = $this->filters;
        ArrayHelper::remove($filters, 'class');
        return (array) $filters;
    }
    public function getFiltersClassName()
    {
        return (string) ArrayHelper::getValue($this->filters, 'class');
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

        return $this->render('@skeeks/cms/backend/actions/views/grid', ['action' => $this]);
    }

    protected $_configKey = null;

    /**
     * @param $key
     * @return $this
     */
    public function setConfigKey($key)
    {
        $this->_configKey = $key;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getConfigKey()
    {
        if ($this->_configKey === null) {
            return $this->uniqueId;
        }

        return $this->_configKey;
    }
}