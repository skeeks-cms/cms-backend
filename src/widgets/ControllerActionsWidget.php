<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\IHasInfoActions;
use skeeks\cms\backend\widgets\assets\ControllerActionsWidgetAsset;
use skeeks\cms\helpers\UrlHelper;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

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
    static public function currentWidget()
    {
        $activeId = null;

        if (\Yii::$app->controller && \Yii::$app->controller->action)
        {
            $activeId = \Yii::$app->controller->action->id;
        }

        return static::widget([
            'actions'    => \Yii::$app->controller->actions,
            'activeId'      => $activeId,
        ]);
    }


    /**
     * @return string
     */
    public function run()
    {
        $actions = $this->actions;

        if (!$actions)
        {
            return "";
        }


        $result = $this->renderListLi();

        ControllerActionsWidgetAsset::register($this->getView());
        return Html::tag("ul", implode($result), $this->options);
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
            if (!$action->visible)
            {
                continue;
            }

            $tagA = $this->renderActionTagA($action);

            $actionDataJson = Json::encode($this->getActionData($action));
            $result[] = Html::tag("li", $tagA,
                [
                    "class" => $this->activeId == $action->id ? "active" : "",
                    "onclick" => "new sx.classes.backend.widgets.Action({$actionDataJson}).go(); return false;"
                ]
            );
        }

        return $result;
    }

    /**
     * @param AdminAction $action
     * @return array
     */
    public function getActionData($action)
    {
        $actionData = array_merge($this->clientOptions, [
            "url"               => $action->url,

            //TODO:// is deprecated
            "isOpenNewWindow"   => $this->isOpenNewWindow,
            "confirm"           => isset($action->confirm) ? $action->confirm : "",
            "method"            => isset($action->method) ? $action->method : "",
            "request"           => isset($action->request) ? $action->request : "",
        ]);

        return $actionData;
    }

    /**
     * @param AdminAction $action
     */
    public function renderActionTagA($action, $tagOptions = [])
    {
        if (!$action->visible)
        {
            return "";
        }

        $icon = '';
        if ($action->icon)
        {
            $icon = Html::tag('span', '', ['class' => $action->icon]);
        }

        return Html::a($icon . '  ' . $action->name, $action->url, $tagOptions);
    }












    /**
     * //TODO:// is deprecated
     * @param AdminAction $action
     * @return UrlHelper
     */
    /*public function getActionUrl($action)
    {
        $url = $action->url;

        if ($this->isOpenNewWindow)
        {
            $url->setSystemParam(\skeeks\cms\modules\admin\Module::SYSTEM_QUERY_EMPTY_LAYOUT, 'true');
        }

        return $url;
    }*/
}
