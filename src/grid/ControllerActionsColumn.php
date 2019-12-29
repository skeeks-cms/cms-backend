<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 11.03.2018
 */

namespace skeeks\cms\backend\grid;

use skeeks\cms\backend\BackendController;
use skeeks\cms\backend\controllers\BackendModelController;
use skeeks\cms\backend\widgets\AjaxControllerActionsWidget;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\modules\admin\widgets\ControllerModelActions;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ControllerActionsColumn extends DataColumn
{
    static public $grids = [];

    /**
     * @var bool
     */
    public $filter = false;

    /**
     * @var BackendModelController|callable
     */
    public $_controller = null;

    /**
     * @var null
     */
    public $isOpenNewWindow = true;

    /**
     * @var bool Включен двойной клик
     */
    public $isDbClick = true;

    /**
     * @var bool Включен клик правой кнопкой
     */
    public $isRightClick = true;

    /**
     * @var bool
     */
    public $isHidden = false;

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
     * @var array
     */
    public $contentOptions = [
        'class' => 'sx-controller-actions-td'
    ];

    /**
     * @var array
     */
    public $headerOptions = [
        'class' => 'sx-controller-actions-th sx-grid-actions'
    ];

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $this->_initAssets();

        return AjaxControllerActionsWidget::widget([
            'controllerId' => $this->controller->uniqueId,
            'modelId'      => $model->{$this->controller->modelPkAttribute},
        ]);

    }

    protected function _initAssets()
    {
        if (!isset(self::$grids[$this->grid->id])) {

            if ($this->isHidden) {
                $this->grid->view->registerCss(<<<CSS
#{$this->grid->id} .sx-controller-actions-td,
#{$this->grid->id} .sx-controller-actions-th
{
    display: none;
}
CSS
                );

            }

            if ($this->isDbClick) {
                $this->grid->view->registerJs(<<<JS

                $("#{$this->grid->id}").on("dblclick", 'tr', function() {
                //$('.sx-first-action', $(this)).click();
                console.log("111");
                var jMainBtn = $(".sx-btn-ajax-actions", $(this));
                
                var jBlocker = sx.block($(this).closest("table"));
                
                jMainBtn.trigger("firstAction");

                jMainBtn.on("firstActionOpen", function() {
                    jBlocker.unblock();
                });
                
                return false;
            });

JS
                );

            }

            if ($this->isRightClick) {
                $this->grid->view->registerJs(<<<JS

                $("#{$this->grid->id}").on("contextmenu", 'tr', function(event) {
                
                var key = $(this).data("key");
                //$(".sx-btn-ajax-actions", $(this)).click();
                var jNewElement = $(".sx-btn-ajax-actions", $(this)).clone();
                
                $("body").append(jNewElement);
                
                jNewElement.css("top", event.clientY);
                jNewElement.css("left", event.clientX);
                jNewElement.css("position", "fixed");
                jNewElement.css("height", "0");
                jNewElement.css("width", "0");
                jNewElement.css("overflow", "hidden");
                jNewElement.removeClass("is-rendered");
                
                jNewElement.click();

                return false;
            });
JS
                );
            }

            $this->grid->view->registerJs(<<<JS
            
            $("#{$this->grid->id}").on("click", '.sx-trigger-action', function() {
                
                $(this).closest("tr").trigger("dblclick");
                return false;
            });
            
JS
            );

            self::$grids[$this->grid->id] = $this->grid->id;
        }
    }
}