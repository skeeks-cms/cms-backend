<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\modules\admin\widgets\ControllerActions;
use yii\filters\AccessControl;
use yii\web\Application;

/**
 * @property string $accessClassName;
 * @property bool   $isVisible;
 *
 * Class TBackendAction
 * @package skeeks\cms\backend
 */
trait TBackendAction
{

    /**
     * @var int приоритет виляет на сортировку
     */
    public $priority = 0;

    /**
     * @var callable
     */
    public $callback;


    /**
     * Ask the question before launching this action?
     * @var string
     */
    public $confirm = '';

    /**
     * @var string
     */
    public $method = 'get';

    /**
     * @var string
     */
    public $request = ''; //ajax


    protected $_isVisible = true;
    /**
     * @var null
     */
    protected $_accessClassName = null;
    /**
     * @return bool
     */
    public function getIsVisible()
    {
        if ($this->_isVisible === false) {
            return false;
        }

        //Проверить разрешения
        if (!$this->isAllow) {
            return false;
        }

        return true;
    }
    /**
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->_isVisible = $isVisible;
        return $this;
    }
    /**
     * @return string
     */
    public function getAccessClassName()
    {
        if ($this->_accessClassName === null) {
            $backend = BackendComponent::getCurrent();
            $class = $backend ? $backend->accessControl : AccessControl::class;

            $this->_accessClassName = $class;
        }

        return (string)$this->_accessClassName;
    }
    /**
     * @param $className
     * @return $this
     */
    public function setAccessClassName($className)
    {
        $this->_accessClassName = $className;
        return $this;
    }
    /**
     * @return $this
     */
    protected function _initUrl()
    {
        if ($this->_url === null) {
            if ($this->controller->module instanceof Application) {
                $this->_url = ['/'.$this->controller->id.'/'.$this->id];
            } else {
                $this->_url = ['/'.$this->controller->module->id.'/'.$this->controller->id.'/'.$this->id];
            }
        }

        return $this;
    }
    /**
     * @return $this
     */
    protected function _initAccess()
    {

        $this->controller->attachBehavior('access'.$this->uniqueId,
            [
                'class' => $this->accessClassName,
                'only'  => [$this->id],
                'rules' => [
                    [
                        'allow'         => true,
                        'matchCallback' => function ($rule, $action) {
                            return $this->isAllow;
                        },
                    ],
                ],
            ]);

        return $this;
    }
}