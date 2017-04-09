<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */
namespace skeeks\cms\backend;

use skeeks\cms\backend\BackendComponent;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\IHasInfo;
use skeeks\cms\IHasUrl;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\traits\THasInfo;
use skeeks\cms\traits\THasUrl;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\ViewAction;
use \skeeks\cms\modules\admin\controllers\AdminController;

/**
 * @property string $accessClassName;
 * @property bool $isVisible;
 *
 * Class TBackendAction
 * @package skeeks\cms\backend
 */
trait TBackendAction
{

    /**
     * @var int приоритет виляет на сортировку
     */
    public $priority = 100;

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
    public $method  = 'get';

    /**
     * @var string
     */
    public $request = ''; //ajax


    protected $_isVisible = true;


    /**
     * @return $this
     */
    protected function _initUrl()
    {
        if ($this->_url === null)
        {
            if ($this->controller->module instanceof Application)
            {
                $this->_url = ['/' . $this->controller->id . '/' . $this->id];
            } else
            {
                $this->_url = ['/' . $this->controller->module->id . '/' . $this->controller->id . '/' . $this->id];
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _initAccess()
    {
        $this->controller->attachBehavior('access' . $this->uniqueId,
        [
            'class'         => $this->accessClassName,
            'only'          => [$this->id],
            'rules'         =>
            [
                [
                    'allow'         => true,
                    'matchCallback' => function($rule, $action)
                    {
                        return $this->isAllow;
                    }
                ],
            ],
        ]);

        return $this;
    }


    /**
     * @return bool
     */
    public function getIsVisible()
    {
        if ($this->_isVisible === false)
        {
            return false;
        }

        //Проверить разрешения
        if (!$this->isAllow)
        {
            return false;
        }

        return true;
    }

    /**
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible(bool $isVisible)
    {
        $this->_isVisible = $isVisible;
        return $this;
    }

    /**
     * @var null
     */
    protected $_accessClassName = null;

    /**
     * @return string
     */
    public function getAccessClassName()
    {
        if ($this->_accessClassName === null)
        {
            $this->_accessClassName = AccessControl::class;
        }

        return (string) $this->_accessClassName;
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
}