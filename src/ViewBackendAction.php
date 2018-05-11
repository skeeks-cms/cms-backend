<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\modules\admin\widgets\ControllerActions;
use yii\base\Event;

/**
 * @property IHasInfoActions $controller
 *
 * Class AdminViewAction
 * @package skeeks\cms\modules\admin\actions
 */
class ViewBackendAction extends BackendAction
{
    /**
     *
     */
    const EVENT_BEFORE_RENDER = 'beforeRender';
    /**
     *
     */
    const EVENT_AFTER_RENDER = 'afterRender';

    /**
     * @var string
     */
    public $defaultView = '';


    public function init()
    {
        if (!$this->defaultView) {
            $this->defaultView = $this->id;
        }

        parent::init();
    }

    /**
     * @return $this|mixed
     */
    public function run()
    {
        if ($this->callback) {
            $result = call_user_func($this->callback, $this);
        } else {
            $result = $this->render($this->defaultView);
        }

        return $result;
    }


    /**
     * Renders a view
     *
     * @param string $viewName view name
     * @return string result of the rendering
     */
    protected function render($viewName, $params = [])
    {
        $e = new Event();
        $this->trigger(self::EVENT_BEFORE_RENDER, $e);
        $result = (string)$e->data;

        $result .= $this->controller->getView()->render($viewName, $params, $this->controller);


        //$result .= $this->controller->render($viewName);

        $e = new Event();
        $this->trigger(self::EVENT_AFTER_RENDER, $e);
        $result .= (string)$e->data;

        return $this->controller->renderContent($result);
    }


}