<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\actions\assets\BackendGridModelActionAsset;
use skeeks\cms\backend\actions\assets\BackendGridModelMultiActionAsset;
use skeeks\cms\backend\BackendComponent;
use skeeks\cms\backend\grid\ControllerActionsColumn;
use skeeks\cms\backend\helpers\BackendIcon;
use skeeks\cms\backend\ViewBackendAction;
use skeeks\cms\backend\widgets\assets\ControllerActionsWidgetAsset;
use skeeks\cms\backend\widgets\ControllerActionsWidget;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\backend\widgets\ListViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use skeeks\cms\rbac\CmsManager;
use skeeks\cms\widgets\DynamicFiltersWidget;
use skeeks\cms\widgets\FiltersWidget;
use skeeks\cms\widgets\GridView;
use skeeks\yii2\config\storages\ConfigDbModelStorage;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\BaseListView;
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
     * Optional page heading rendered above the list.
     *
     * Supported keys: title, description, icon, options, action and actions.
     * An action may reference a standard controller action through
     * `backendAction`.
     *
     * Null lets presentationMode decide, false explicitly disables the header.
     *
     * @var array|false|null
     */
    public $pageHeader = null;

    const PRESENTATION_LEGACY = 'legacy';
    const PRESENTATION_AUTO = 'auto';
    const PRESENTATION_PAGE = 'page';
    const PRESENTATION_TABS = 'tabs';

    /**
     * - legacy: preserve the historical controller actions bar;
     * - page: render a page header and move create into it by default;
     * - tabs: preserve controller actions as section navigation;
     * - auto: choose page for index/create-only controllers, tabs otherwise.
     *
     * @var string
     */
    public $presentationMode = self::PRESENTATION_LEGACY;

    /**
     * Controller actions displayed in the navigation above the page.
     *
     * - null: preserve all visible controller actions;
     * - false: hide the navigation;
     * - array: display only the listed action IDs.
     *
     * @var array|false|null
     */
    public $navigationActionIds = null;

    /**
     * Empty collection presentation passed to GridViewWidget.
     *
     * Supported keys: title, description, icon, options and action
     * (label, url, icon, options).
     *
     * @var array|false
     */
    public $emptyState = [];

    /**
     * Empty search result presentation.
     *
     * @var array|false
     */
    public $noResultsState = [];

    /**
     * Hide filters from regular users while the whole list stays small.
     *
     * @var bool
     */
    public $hideFiltersOnSmallLists = true;

    /**
     * @var int
     */
    public $smallListLimit = 5;

    /**
     * Users who can manage backend showings keep the complete list toolset.
     *
     * @var bool
     */
    public $alwaysShowFiltersForManagers = true;

    /**
     * @var GridViewWidget
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

        if ($this->emptyState !== false) {
            $this->emptyState = ArrayHelper::merge([
                'title' => \Yii::t('skeeks/backend', 'Записей пока нет'),
                'description' => \Yii::t('skeeks/backend', 'Здесь появятся записи, когда они будут добавлены.'),
                'icon' => 'fa fa-inbox',
            ], (array)$this->emptyState);
        }

        if ($this->noResultsState !== false) {
            $this->noResultsState = ArrayHelper::merge([
                'title' => \Yii::t('skeeks/backend', 'Ничего не найдено'),
                'description' => \Yii::t('skeeks/backend', 'Попробуйте изменить запрос или сбросить выбранные фильтры.'),
                'icon' => 'fa fa-search',
            ], (array)$this->noResultsState);
        }


        $r = new \ReflectionClass($this->backendShowing);
        $backendShowingId = $this->backendShowing->id;
        $backendShowingClassName = $r->getName();

        $configuredViewClass = (string)ArrayHelper::getValue($this->grid, 'class', GridViewWidget::class);
        $isItemsView = is_a($configuredViewClass, ListViewWidget::class, true);

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
                $editIcon = '';
                $callableDataInput = '';
                if (\Yii::$app->user->can(CmsManager::PERMISSION_ROLE_ADMIN_ACCESS)) {

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
                    $editData = $gridViewWidget->editData;
                    $callAttributes = (array)ArrayHelper::getValue($editData, 'callAttributes', []);
                    $editData['callAttributes'] = array_intersect_key($callAttributes, array_flip([
                        'caption',
                        'visibleColumns',
                        'pageParam',
                        'pageSizeParam',
                        'defaultPageSize',
                        'pageSizeLimitMin',
                        'pageSizeLimitMax',
                        'defaultOrder',
                        'autoColumns',
                        'disableAutoColumns',
                        'contextData',
                        'configBehaviorData',
                    ]));

                    $availableColumns = (array)ArrayHelper::remove($editData, 'availableColumns', []);
                    if ($availableColumns) {
                        $availableColumnsCacheKey = 'sx-grid-available-columns-'.md5($gridViewWidget->id.microtime(true).mt_rand());
                        \Yii::$app->cache->set($availableColumnsCacheKey, $availableColumns, 3600);

                        $selectedColumnCodes = array_unique(ArrayHelper::merge(
                            (array)ArrayHelper::getValue($editData, 'visibleColumns', []),
                            (array)ArrayHelper::getValue($editData, 'attributes.visibleColumns', []),
                            (array)ArrayHelper::getValue($editData, 'callAttributes.visibleColumns', [])
                        ));
                        $selectedColumns = [];
                        foreach ($selectedColumnCodes as $columnCode) {
                            if (array_key_exists($columnCode, $availableColumns)) {
                                $selectedColumns[$columnCode] = $availableColumns[$columnCode];
                            }
                        }

                        $editData['availableColumns'] = $selectedColumns;
                        $editData['availableColumnsCacheKey'] = $availableColumnsCacheKey;
                        $editData['availableColumnsUrl'] = Url::to([
                            BackendComponent::getCurrent()->backendShowingControllerRoute.'/component-callable-data',
                            'key' => $availableColumnsCacheKey,
                            'componentClassName' => $gridViewWidget::className(),
                        ]);
                    }

                    $callableDataInput = Html::textarea('callableData', base64_encode(serialize($editData)), [
                        'id'    => $gridViewWidget->id."-edit",
                        'style' => 'display: none;',
                    ]);

                    $editIcon = Html::a(BackendIcon::render('settings', ['size' => 18]),
                        '#', [
                            'class'   => 'btn btn-sm',
                            'title'   => 'Настроить таблицу',
                            'aria-label' => 'Настроить таблицу',
                            'onclick' => new JsExpression(<<<JS
            new sx.classes.backend.EditComponent({$editComponent}); return false;
JS
                            ),

                        ]);

                }


                $url = Url::current([
                    $gridViewWidget->exportParam => $gridViewWidget->id,
                ]);

                return '<div class="sx-grid-settings">'.

                    Html::a(BackendIcon::render('download', ['size' => 18]), $url, [
                        'target'    => '_blank',
                        'data-pjax' => '0',
                        'title'     => 'Экспорт в CSV',
                        'aria-label' => 'Экспортировать таблицу в CSV',
                        'class'     => 'btn btn-sm',
                    ])
                    .  $editIcon
                    .$callableDataInput.

                    Html::a(BackendIcon::render('expand', ['size' => 18]), '#', [
                        'class'   => 'btn btn-sm',
                        'title'   => 'Развернуть таблицу',
                        'aria-label' => 'Развернуть таблицу',
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
                /*'serial'   => [
                    'class'   => 'yii\grid\SerialColumn',
                    'visible' => false,
                ],*/
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

        if ($isItemsView) {
            $defaultGrid = [
                'class'          => $configuredViewClass,
                'modelClassName' => $this->modelClassName,
                'options'        => [
                    'class' => 'sx-backend-list',
                ],
            ];
        } elseif ($this->isStandartAjaxPager) {
            $defaultGrid['pager'] = [
                'class'              => \skeeks\cms\backend\widgets\BackendScrollAndSpPager::class,
                'container'          => '.grid-view tbody',
                'item'               => 'tr',
                //'triggerOffset'               => '2',
                'paginationSelector' => '.grid-view .pagination',
                'triggerTemplate'    => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
            ];
        }

        parent::init();
        /*
         * A standard collection may hide ControllerActionsColumn and render
         * its client-facing primary link manually. Keep the standard backend
         * action contract available for both GridViewWidget and
         * ListViewWidget instead of forcing every controller/cell renderer to
         * register the JavaScript bundle itself.
         */
        ControllerActionsWidgetAsset::register(\Yii::$app->view);

        $defaultFilters = [
            //'class'              => \skeeks\cms\backend\widgets\SearchAndFiltersWidget::class,
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
        $this->grid['options'] = (array)ArrayHelper::getValue($this->grid, 'options', []);
        Html::addCssClass(
            $this->grid['options'],
            $isItemsView ? 'sx-backend-list' : 'sx-backend-grid'
        );
        if ($this->filters === false) {
            $this->filters = false;
        } else {
            $this->filters = (array)ArrayHelper::merge($defaultFilters, (array)$this->filters);
        }


        
        /*if (YII_ENV_DEV) {
            print_r("тут");
            print_r(\Yii::$app->assetManager->bundles);die;
        }*/
        
        
        

    }

    /**
     * @return string
     */
    public function renderBeforeTable(GridViewWidget $gridViewWidget)
    {
        BackendGridModelActionAsset::register(\Yii::$app->view);

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

        BackendGridModelMultiActionAsset::register($gridViewWidget->view);

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
        if (!array_key_exists('emptyState', $grid)) {
            if ($this->hasActiveFilters()) {
                $grid['emptyState'] = $this->noResultsState;
            } else {
                $emptyState = $this->emptyState;
                if ($emptyState !== false) {
                    $configuredAction = (array)ArrayHelper::getValue($emptyState, 'action', []);
                    $backendActionId = ArrayHelper::remove($configuredAction, 'backendAction');

                    if ($backendActionId) {
                        $defaultAction = $this->getBackendActionPresentation($backendActionId);
                        if ($defaultAction) {
                            $emptyState['action'] = ArrayHelper::merge($defaultAction, $configuredAction);
                        }
                    } elseif (!$configuredAction) {
                        $defaultAction = $this->getBackendActionPresentation();
                        if ($defaultAction) {
                            $emptyState['action'] = $defaultAction;
                        }
                    }
                }
                $grid['emptyState'] = $emptyState;
            }
        }
        return (array)$grid;
    }

    /**
     * Build a conventional empty-state action from the controller's create
     * action. Explicit emptyState.action configuration always takes priority.
     *
     * Set emptyState.action.backendAction to reuse another controller action
     * while overriding its label, icon or link options.
     *
     * @param string $actionId
     * @return array
     */
    public function getBackendActionPresentation($actionId = 'create', array $config = [])
    {
        /*
         * Reuse the controller-owned action instance first. Related grids and
         * other composition actions may already have enriched its URL with
         * parent context. Recreating it here would silently drop those runtime
         * parameters from page-header and empty-state actions.
         */
        $backendAction = ArrayHelper::getValue($this->controller->actions, $actionId);
        if (!$backendAction) {
            $backendAction = $this->controller->createAction($actionId);
        }
        if (!$backendAction || !$backendAction->isVisible || !$backendAction->url) {
            return [];
        }

        $actionsWidget = new ControllerActionsWidget();
        $actionData = $actionsWidget->getActionData($backendAction);
        ControllerActionsWidgetAsset::register(\Yii::$app->view);

        return ArrayHelper::merge([
            'label' => $backendAction->name ?: \Yii::t('skeeks/backend', 'Добавить'),
            'url' => ArrayHelper::getValue($actionData, 'url', $backendAction->url),
            'icon' => $backendAction->icon ?: 'fa fa-plus',
            'variant' => 'primary',
            'options' => [
                'data-pjax' => '0',
                'onclick' => new JsExpression(
                    'new sx.classes.backend.widgets.Action('.Json::encode($actionData).').go(); return false;'
                ),
            ],
        ], $config);
    }

    /**
     * @return array|false
     */
    public function getPageHeaderConfig()
    {
        if ($this->pageHeader === false) {
            return false;
        }

        $resolvedMode = $this->getResolvedPresentationMode();
        if ($this->pageHeader === null && $resolvedMode !== self::PRESENTATION_PAGE) {
            return false;
        }

        $pageHeader = (array)$this->pageHeader;
        if (!ArrayHelper::getValue($pageHeader, 'title')) {
            $pageHeader['title'] = $this->controller->name ?: $this->name;
        }

        $hasConfiguredActions = array_key_exists('actions', $pageHeader)
            || array_key_exists('action', $pageHeader);
        $actions = ArrayHelper::getValue($pageHeader, 'actions', []);
        $legacyAction = ArrayHelper::getValue($pageHeader, 'action');
        if (!$actions && $legacyAction) {
            $actions = [$legacyAction];
        } elseif (!$hasConfiguredActions && $resolvedMode === self::PRESENTATION_PAGE) {
            $actions = ['create'];
        }

        $resolvedActions = [];
        foreach ((array)$actions as $key => $actionConfig) {
            if (is_string($actionConfig)) {
                $actionConfig = ['backendAction' => $actionConfig];
            } elseif (!is_array($actionConfig)) {
                continue;
            }

            if (is_string($key) && !ArrayHelper::getValue($actionConfig, 'backendAction')) {
                $actionConfig['backendAction'] = $key;
            }

            if (!array_key_exists('variant', $actionConfig)) {
                $actionConfig['variant'] = $resolvedActions ? 'secondary' : 'primary';
            }

            $backendActionId = ArrayHelper::remove($actionConfig, 'backendAction');
            if ($backendActionId) {
                $actionConfig = $this->getBackendActionPresentation($backendActionId, $actionConfig);
            }

            if (
                ArrayHelper::getValue($actionConfig, 'label')
                && ArrayHelper::getValue($actionConfig, 'url')
            ) {
                $resolvedActions[] = $actionConfig;
            }
        }

        $pageHeader['actions'] = $resolvedActions;
        ArrayHelper::remove($pageHeader, 'action');

        return $pageHeader;
    }

    /**
     * @return string
     */
    public function getResolvedPresentationMode()
    {
        if ($this->presentationMode !== self::PRESENTATION_AUTO) {
            return $this->presentationMode;
        }

        $visibleActionIds = [];
        foreach ((array)$this->controller->actions as $id => $backendAction) {
            if (isset($backendAction->isVisible) && $backendAction->isVisible) {
                $visibleActionIds[] = (string)$id;
            }
        }

        $sectionActionIds = array_diff($visibleActionIds, ['index', 'create']);
        return $sectionActionIds
            ? self::PRESENTATION_TABS
            : self::PRESENTATION_PAGE;
    }

    /**
     * @return array|false|null
     */
    public function getResolvedNavigationActionIds()
    {
        if ($this->navigationActionIds !== null) {
            return $this->navigationActionIds;
        }

        return $this->getResolvedPresentationMode() === self::PRESENTATION_PAGE
            ? false
            : null;
    }

    /**
     * @return bool
     */
    public function hasActiveFilters()
    {
        if (!$this->filters) {
            return false;
        }

        $formName = (string)ArrayHelper::getValue(
            $this->filters,
            'filtersModel.formName',
            'f'.$this->id
        );

        return $formName !== '' && !empty(\Yii::$app->request->get($formName));
    }

    /**
     * @param BaseListView $listView
     * @return bool
     */
    public function shouldDisplayFilters(BaseListView $listView)
    {
        if (!$this->filters) {
            return false;
        }

        if (
            $this->alwaysShowFiltersForManagers
            && BackendComponent::getCurrent()->canManageBackendShowings
        ) {
            return true;
        }

        if ($this->hasActiveFilters()) {
            return true;
        }

        if (!$this->hideFiltersOnSmallLists) {
            return true;
        }

        return $listView->dataProvider->getTotalCount() > $this->smallListLimit;
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
