<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class DropdownControllerActionsWidget
 *
 * @package skeeks\cms\backend\widgets
 */
class DropdownControllerActionsWidget extends ControllerActionsWidget
{

    public $options =
    [
        "class" => "dropdown-menu"
    ];

    /**
     * @var string
     */
    public $wrapperOptions = [
        'class' => 'dropdown'
    ];

    /**
     * @var bool
     */
    public $renderFirstAction = true;

    /**
     * @return string
     */
    public function run()
    {
        $actions = $this->actions;

        $firstAction = '';
        if ($actions && is_array($actions) && count($actions) >= 1)
        {
            $firstAction = array_shift($actions);
        }

        $style = '';
        $firstActionString = '';

        if ($firstAction && $this->renderFirstAction && ($firstAction->icon || $firstAction->image))
        {
            $actionDataJson = Json::encode($this->getActionData($firstAction));

            $tagOptions = [
                "onclick"   => "new sx.classes.backend.widgets.Action({$actionDataJson}).go(); return false;",
                "class"     => "btn btn-xs btn-default sx-row-action",
                "title"     => $firstAction->name
            ];

            $firstActionString = Html::a($this->getSpanIcon($firstAction), $this->getActionUrl($firstAction), $tagOptions);
            $style = 'min-width: 43px;';
        }

        $this->wrapperOptions['title'] = \Yii::t('skeeks/cms','Possible actions');

        $content = "
                    <span class=\"btn-group\" role=\"group\" style='{$style}'>
                        {$firstActionString}
                        <a type=\"button\" class='btn btn-xs btn-default sx-btn-caret-action' data-toggle=\"dropdown\">
                           <i class=\"fa fa-caret-down\"></i>
                        </a>" .
                    parent::run()

                . "
                    </span>
                ";

        return Html::tag("span", $content, $this->wrapperOptions);

    }

    /**
     * @param AdminAction $action
     * @return string
     */
    public function getSpanIcon($action)
    {
        $icon = '';
        if ($action->icon)
        {
            $icon = Html::tag('span', '', ['class' => $action->icon]);
        }
        return $icon;
    }
}