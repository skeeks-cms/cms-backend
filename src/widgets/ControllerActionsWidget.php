<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\backend\IHasInfoActions;
use skeeks\cms\backend\widgets\assets\ControllerActionsWidgetAsset;
use skeeks\cms\helpers\UrlHelper;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 *
 * echo \skeeks\cms\backend\widgets\ControllerActionsWidget::widget([
        'actions' => ['create' => $actionCreate],
        'clientOptions'     => ['pjax-id' => $pjax->id],
        'isOpenNewWindow'   => true,
        'tag'               => 'div',
        'itemWrapperTag'    => 'span',
        'itemTag'           => 'button',
        'itemOptions'       => ['class' => 'btn btn-default'],
        'options'           => ['class' => 'sx-controll-actions'],
    ]);
*
 * Class ControllerActionsWidget
 * @package skeeks\cms\backend\widgets
 */
class ControllerActionsWidget extends Widget
{

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @var null
     */
    public $activeId          = null;

    /**
     * @var array
     */
    public $options               =
    [
        "class" => "nav nav-pills sx-nav"
    ];

    /**
     * @var string
     */
    public $tag               = "ul";

    /**
     * @var int
     */
    public $minViewCount               = 2;



    /**
     * @var string
     */
    public $itemWrapperTag               = "li";
    /**
     * @var array
     */
    public $itemWrapperOptions           = [];


    /**
     * @var string
     */
    public $itemTag               = "a";
    /**
     * @var array
     */
    public $itemOptions           = [];


    /**
     * @var array
     */
    public $clientOptions           = [];

    /**
     * @var bool
     */
    public $isOpenNewWindow = false;

    /**
     * @return string
     */
    static public function currentWidget($config = [])
    {
        $activeId = null;

        if (\Yii::$app->controller && \Yii::$app->controller->action)
        {
            $activeId = \Yii::$app->controller->action->id;
        }

        return static::widget(ArrayHelper::merge($config, [
            'actions'    => \Yii::$app->controller->actions,
            'activeId'      => $activeId,
        ]));
    }


    /**
     * @return string
     */
    public function run()
    {
        ControllerActionsWidgetAsset::register($this->getView());
        $actions = $this->actions;

        if (!$actions || count($actions) < $this->minViewCount)
        {
            return "";
        }

        $result = $this->renderListLi();

        ControllerActionsWidgetAsset::register($this->getView());
        return Html::tag($this->tag, implode($result), $this->options);
    }

    /**
     * @return array
     */
    public function renderListLi()
    {
        $result = [];

        $actions = $this->actions;

        if (!$actions)
        {
            return [];
        }

        foreach ($actions as $id => $action)
        {
            if (!$action->isVisible)
            {
                continue;
            }

            $tagA = $this->renderActionTagA($action);
            $actionDataJson = Json::encode($this->getActionData($action));

            $options = $this->itemWrapperOptions;
            $options['onclick'] = "new sx.classes.backend.widgets.Action({$actionDataJson}).go(); return false;";

            if ($this->activeId == $action->id)
            {
                Html::addCssClass($options, 'active');
            }

            $result[] = Html::tag($this->itemWrapperTag, $tagA, $options);
        }

        return $result;
    }

    /**
     * @param AdminAction $action
     * @return array
     */
    public function getActionData($action)
    {
        $isOpenNewWindow = false;
        if ($this->isOpenNewWindow) {
            $isOpenNewWindow = true;
        } elseif (isset($action->isOpenNewWindow)) {
            $isOpenNewWindow = $action->isOpenNewWindow;
        }

        $actionData = array_merge($this->clientOptions, [
            "url"               => $this->getActionUrl($action),

            //TODO:// is deprecated
            "isOpenNewWindow"   => $isOpenNewWindow,
            "confirm"           => isset($action->confirm) ? $action->confirm : "",
            "method"            => isset($action->method) ? $action->method : "",
            "request"           => isset($action->request) ? $action->request : "",
        ]);

        return $actionData;
    }

    /**
     * @param BackendAction $action
     */
    public function renderActionTagA($action, $tagOptions = [])
    {
        if (!$action->isVisible)
        {
            return "";
        }

        $options = $this->itemOptions;
        $options['href'] = $this->getActionUrl($action);

        $icon = '';
        if ($action->icon)
        {
            $icon = Html::tag('span', '', ['class' => $action->icon]);
        }

        if ($this->activeId == $action->id)
        {
            Html::addCssClass($options, 'active');
        }


        return Html::tag($this->itemTag, $icon . '  ' . $action->name, $options);
    }

    /**
     * @param $action
     *
     * @return array
     */
    public function getActionUrl($action)
    {
        $isOpenNewWindow = false;
        if ($this->isOpenNewWindow) {
            $isOpenNewWindow = true;
        } elseif (isset($action->isOpenNewWindow)) {
            $isOpenNewWindow = $action->isOpenNewWindow;
        }

        if (is_array($action->urlData) && $isOpenNewWindow)
        {
            $action->url = BackendUrlHelper::createByParams($action->urlData)
                ->enableEmptyLayout()
                ->enableNoActions()
                ->params
            ;
        };

        return $action->url;
    }
}
