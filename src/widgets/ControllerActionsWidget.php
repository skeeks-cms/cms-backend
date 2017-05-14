<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\BackendAction;
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
            if (!$action->isVisible)
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
            "url"               => $this->getActionUrl($action),

            //TODO:// is deprecated
            "isOpenNewWindow"   => $this->isOpenNewWindow,
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

        $icon = '';
        if ($action->icon)
        {
            $icon = Html::tag('span', '', ['class' => $action->icon]);
        }

        return Html::a($icon . '  ' . $action->name, $this->getActionUrl($action), $tagOptions);
    }

    /**
     * @param $action
     *
     * @return array
     */
    public function getActionUrl($action)
    {
        if (is_array($action->urlData) && $this->isOpenNewWindow)
        {
            $action->url = UrlHelper::construct($action->urlData)
                ->setSystemParam(\skeeks\cms\modules\admin\Module::SYSTEM_QUERY_EMPTY_LAYOUT, 'true')
                ->setSystemParam(\skeeks\cms\modules\admin\Module::SYSTEM_QUERY_NO_ACTIONS_MODEL, 'true')
                ->toArray()
            ;
        };

        return $action->url;
    }
}
