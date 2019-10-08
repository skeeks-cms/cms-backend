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
use skeeks\cms\backend\widgets\assets\ControllerActionsWidgetAsset;
use skeeks\yii2\contextmenu\JqueryContextMenuWidget;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ContextMenuControllerActionsWidget extends ContextMenuWidget
{
    /**
     * @var BackendAction[]
     */
    public $actions = [];
    
    /**
     * @var bool
     */
    public $isOpenNewWindow = false;

    public function run()
    {
        ControllerActionsWidgetAsset::register($this->getView());
        $actions = $this->actions;


        if (!$actions)
        {
            return "";
        }
        
        $this->items = [];

        $firstAction = "";
        $counter = 0;
        foreach ($actions as $id => $action)
        {
            $counter ++;

            if (!$action->isVisible)
            {
                continue;
            }

            $actionDataJson = Json::encode($this->getActionData($action));

            $options = [];
            $options['icon'] = $action->icon;
            $options['name'] = "<i class='{$action->icon}'></i> " . $action->name;
            $options['callback'] = new \yii\web\JsExpression("function(key, options) {
                new sx.classes.backend.widgets.Action({$actionDataJson}).go();
            }");

            if ($counter == 1) {
                $firstAction = Html::a("test", "#", [
                    'onclick' => "new sx.classes.backend.widgets.Action({$actionDataJson}).go(); return false;",
                    'style' => 'display: none;',
                    'class' => 'sx-first-action'
                ]);
            }


            if (isset($this->items[$action->id])) {
                $this->items[$action->id . uniqid()] = $options;
            } else {
                $this->items[$action->id] = $options;
            }
            //$this->items[] = $options;
        }
        
        return $firstAction . parent::run();
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
     * @param $action
     *
     * @return array
     */
    public function getActionUrl($action)
    {
        if (is_array($action->urlData) && $this->isOpenNewWindow)
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
