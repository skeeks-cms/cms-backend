<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\backend\grid\ControllerActionsColumn;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
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
    public $gridClassName;

    /**
     * @var array
     */
    public $gridConfig = [];

    /**
     * @var
     */
    protected $_modelClassName;

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return (string)$this->controller->modelClassName;
    }

    public function init()
    {
        if (!$this->gridClassName) {
            $this->gridClassName = GridViewCmsWidget::class;
        }

        if (!isset($this->gridConfig['modelClassName'])) {
            $this->gridConfig['modelClassName'] = $this->modelClassName;
        }

        if (!isset($this->gridConfig['gridClassName'])) {
            $this->gridConfig['gridClassName'] = GridViewWidget::class;
        }

        if (!isset($this->gridConfig['namespace'])) {
            $this->gridConfig['namespace'] = $this->uniqueId;
        }

        //Колонки по умолчанию
        if (isset($this->gridConfig['columns'])) {
            $this->gridConfig['columns'] = ArrayHelper::merge([
                'checkbox' => [
                    'class' => 'skeeks\cms\grid\CheckboxColumn'
                ],
                'actions' => [
                    'class'                 => ControllerActionsColumn::class,
                    'controller'            => $this->controller,
                    'isOpenNewWindow'       => true
                ],
                'serial' => [
                    'class' => 'yii\grid\SerialColumn',
                    'visible' => false
                ]
            ], $this->gridConfig['columns']);
        }

        parent::init();
    }

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        return $this->controller->render('@skeeks/cms/backend/actions/views/grid');
    }










    protected $_initMultiOptions = null;
    protected $_buttonsMulti = null;
    protected $_additionalsMulti = null;

    /**
     * @return string
     */
    public function renderBeforeTable()
    {
        $multiActions = [];
        if ($this->controller) {
            $multiActions = $this->adminController->modelMultiActions;
        }

        if (!$multiActions) {
            return parent::renderBeforeTable();
        }

        $this->_initMultiActions();
        $this->beforeTableLeft = $this->_buttonsMulti;

        return parent::renderBeforeTable();
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
        $this->afterTableLeft = $this->_buttonsMulti . $this->_additionalsMulti;

        return parent::renderAfterTable();
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
            'id' => $this->id,
            'requestPkParamName' => $this->controller->requestPkParamName
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
            'class' => 'sx-select-full-all'
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
}