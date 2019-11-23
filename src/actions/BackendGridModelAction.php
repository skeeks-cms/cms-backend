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
use skeeks\cms\backend\ViewBackendAction;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use skeeks\cms\modules\admin\widgets\gridViewStandart\GridViewStandartAsset;
use skeeks\cms\widgets\DynamicFiltersWidget;
use skeeks\cms\widgets\FiltersWidget;
use skeeks\cms\widgets\GridView;
use skeeks\yii2\config\storages\ConfigDbModelStorage;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
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
     * Заполняется после рендеринга шаблона
     * @var null|GridView
     */
    public $gridObject = null;

    /**
     * @var \skeeks\cms\backend\widgets\FiltersWidget
     */
    public $filters;

    /**
     * Включить стандартную ajax навигацию
     * @var bool
     */
    public $isStandartAjaxPager = true;

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

    /**
     * @throws \ReflectionException
     */
    public function init()
    {
        if (!$this->icon) {
            $this->icon = "fa fa-list";
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

                \Yii::$app->request->url;

                $url = Url::current([
                    $gridViewWidget->exportParam => $gridViewWidget->id,
                ]);

                return '<div class="sx-grid-settings">'.

                    Html::a('<i class="fa fa-download"></i>', $url, [
                        'target'    => '_blank',
                        'data-pjax' => '0',
                        'title'     => 'Экспорт в CSV',
                        'class'     => 'btn btn-sm',
                    ])
                    .
                    Html::a('<i class="fa fa-cog"></i>',
                        '#', [
                            'class'   => 'btn btn-sm',
                            'onclick' => new JsExpression(<<<JS
            new sx.classes.backend.EditComponent({$editComponent}); return false;
JS
                            ),

                        ])

                    .$callableDataInput.

                    Html::a('<i class="fa fa-expand"></i>', '#', [
                        'class'   => 'btn btn-sm',
                        'onclick' => new JsExpression(<<<JS
                        if (!jQuery(this).closest('.sx-grid-view').hasClass('sx-grid-view-full')) {
                            jQuery(this).closest('.sx-grid-view').addClass('sx-grid-view-full'); return false;
                            jQuery('body').addClass('has-sx-grid-view-full');
                        } else {
                            jQuery(this).closest('.sx-grid-view').removeClass('sx-grid-view-full'); return false;
                            jQuery('body').removeClass('has-sx-grid-view-full');
                        }
            
JS
                        ),
                    ])


                    ."</div>";
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
                    /*'label'         => \Yii::t('skeeks/backend', 'Actions'),*/
                ],

            ],
        ];

        if ($this->isStandartAjaxPager) {
            $defaultGrid['pager'] = [
                'class'              => \skeeks\cms\themes\unify\widgets\ScrollAndSpPager::class,
                'container'          => '.grid-view tbody',
                'item'               => 'tr',
                //'triggerOffset'               => '2',
                'paginationSelector' => '.grid-view .pagination',
                'triggerTemplate'    => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
            ];
        }

        parent::init();

        $defaultFilters = [
            'class'              => \skeeks\cms\backend\widgets\FiltersWidget::class,
            'filtersModel'       => [
                'formName' => 'f'.$this->id,
            ],
            'activeForm'         => [
                'action'  => $this->getShowingUrl($this->getBackendShowing()),
                'options' => [
                    'data' => [
                        'real-action' => $this->getShowingUrl($this->getBackendShowing()),
                    ],
                ],
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

        /*print_r($this->url);
        die;*/

        $this->grid = (array)ArrayHelper::merge($defaultGrid, (array)$this->grid);
        if ($this->filters === false) {
            $this->filters = false;
        } else {
            $this->filters = (array)ArrayHelper::merge($defaultFilters, (array)$this->filters);
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