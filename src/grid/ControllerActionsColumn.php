<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 11.03.2018
 */

namespace skeeks\cms\backend\grid;

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
    public $isHidden = true;

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
        'class' => 'sx-controller-actions-td',
    ];

    /**
     * @var array
     */
    public $headerOptions = [
        'class' => 'sx-controller-actions-th sx-grid-actions',
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
            'options'      => [
                'class' => 'sx-collection-item__action sx-collection-item__action--icon',
                'aria-label' => \Yii::t('skeeks/cms', 'Действия'),
            ],
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
                var jMainBtn = $(".sx-btn-ajax-actions:first", $(this));
                
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

                event.preventDefault();

                var jRow = $(this);
                var jMainBtn = jRow.children('.sx-controller-actions-td')
                    .find('.sx-btn-ajax-actions:first');

                if (!jMainBtn.length) {
                    return false;
                }

                $('.sx-grid-context-actions-anchor').each(function() {
                    var jAnchor = $(this);
                    if (jAnchor.data('bs.popover')) {
                        try {
                            jAnchor.popover('dispose');
                        } catch (e) {
                            jAnchor.popover('destroy');
                        }
                    }
                    jAnchor.remove();
                });

                var jNewElement = jMainBtn.clone();

                jNewElement
                    .empty()
                    .removeClass('sx-collection-item__action sx-collection-item__action--icon')
                    .addClass('sx-grid-context-actions-anchor')
                    .attr('aria-hidden', 'true');

                $("body").append(jNewElement);

                jNewElement.css({
                    top: event.clientY,
                    left: event.clientX,
                    position: 'fixed'
                });
                jNewElement.removeClass("is-rendered");

                jNewElement.one('hidden.bs.popover', function() {
                    var jAnchor = $(this);
                    try {
                        jAnchor.popover('dispose');
                    } catch (e) {
                        jAnchor.popover('destroy');
                    }
                    jAnchor.remove();
                });

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
