<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\backend\models\BackendShowing;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use skeeks\cms\widgets\DynamicFiltersWidget;
use skeeks\yii2\config\DynamicConfigModel;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
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
        $defaultGrid = [
            'class'          => GridViewWidget::class,
            'modelClassName' => $this->modelClassName,
            'config'         => [
                'configKey' => $this->uniqueId,
            ],
            'columns'        => [
                'checkbox' => [
                    'class' => 'skeeks\cms\grid\CheckboxColumn',
                ],
                /*'actions'  => [
                    'class'           => ControllerActionsColumn::class,
                    'controller'      => $this->controller,
                    'isOpenNewWindow' => true,
                ],*/
                'serial'   => [
                    'class'   => 'yii\grid\SerialColumn',
                    'visible' => false,
                ],
            ],
        ];

        $defaultFilters = [
            'class' => DynamicConfigModel::class
        ];

        $this->grid = (array)ArrayHelper::merge($defaultGrid, (array)$this->grid);
        $this->filters = (array)ArrayHelper::merge($defaultFilters, (array)$this->filters);

        $this->filters = \Yii::createObject($this->filters);

        parent::init();


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
            }

            //Defauilt filter
            $backendShowing = BackendShowing::find()
                ->where(['key' => $this->uniqueId])
                ->andWhere(['cms_user_id' => \Yii::$app->user->id])
                ->andWhere(['is_default' => 1])
                ->one();

            if (!$backendShowing) {
                $backendShowing = new BackendShowing([
                    'key'         => $this->uniqueId,
                    'cms_user_id' => \Yii::$app->user->id,
                    'is_default'  => 1,
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
     * @return array|BackendShowing[]
     */
    public function getBackendShowings()
    {
        return BackendShowing::find()->where([
            'key' => $this->uniqueId,
        ])->orderBy(['priority' => SORT_ASC])->all();
    }
}