<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 11.03.2018
 */

namespace skeeks\cms\backend\grid;

use skeeks\cms\backend\controllers\BackendModelController;
use skeeks\cms\backend\widgets\DropdownControllerActionsWidget;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\modules\admin\widgets\ControllerModelActions;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;

/**
 * Class ControllerActionsColumn
 * @package skeeks\cms\backend\grid
 */
class ControllerActionsColumn extends DataColumn
{
    static public $grids = [];
    public $filter = false;
    /**
     * @var BackendModelController|callable
     */
    public $_controller = null;

    /**
     * @var null
     */
    public $isOpenNewWindow = null;

    /**
     * @var array
     */
    public $clientOptions = [];
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->controller) {
            throw new InvalidConfigException("controller - ".\Yii::t('skeeks/cms', "not specified").".");
        }
    }

    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    public function getController()
    {
        if (is_callable($this->_controller)) {
            $this->_controller = call_user_func($this->_controller, $this);
        }

        return $this->_controller;
    }
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        //print_r($this->grid->pjax);die;
        //Строемся в гриде который использует pjax
        /*if ($this->grid->pjax) {
            $this->clientOptions['pjax-id'] = $this->grid->pjax->options['id'];
        }*/

        $controller = clone $this->controller;
        $controller->model = $model;

        $this->gridDoubleClickAction();

        /*return DropdownControllerActionsWidget::widget([
            "actions"         => $controller->modelActions,
            "isOpenNewWindow" => $this->isOpenNewWindow,
            "clientOptions"   => $this->clientOptions,
        ]);*/

        return \skeeks\cms\backend\widgets\ContextMenuControllerActionsWidget::widget([
            'actions' => $controller->modelActions,
            'isOpenNewWindow' => $this->isOpenNewWindow,
            'rightClickSelectors' => ['tr[data-key=' . $key . ']'],
            'button' => [
                'class' => 'btn btn-xs btn-default sx-btn-caret-action',
                'style' => '',
                'tag' => 'a',
                'label' => '<span class="caret"></span>',
            ]
        ]);
    }

    protected function gridDoubleClickAction()
    {
        if (!isset(self::$grids[$this->grid->id])) {
            $this->grid->view->registerJs(<<<JS
            $('tr', $("#{$this->grid->id}")).on('dblclick', function()
            {
                //$('.sx-row-action', $(this)).click();
                $(this).contextmenu();
            });
JS
            );
            self::$grids[$this->grid->id] = $this->grid->id;
        }
    }
}