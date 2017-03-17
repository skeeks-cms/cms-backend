<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\traits;

/**
 * @property $modelClassName;
 * @property $modelDefaultAction;
 * @property $modelShowAttribute;
 * @property $modelPkAttribute;
 * @property $requestPkParamName;
 *
 * Class BackendModelControllerTrait
 * @package skeeks\cms\backend
 */
trait TBackendModelController
{
    /**
     * @var string
     */
    protected $_modelClassName = '';

    /**
     * Required to fill!
     * The model class with which the controller operates.
     *
     * @example ActiveRecord::class;
     * @var string
     */
    public function getModelClassName()
    {
        return $this->_modelClassName;
    }

    /**
     * @param $modelClassName
     * @return $this
     */
    public function setModelClassName($modelClassName)
    {
        $this->_modelClassName = $modelClassName;
        return $this;
    }


    /**
     * @var string
     */
    protected $_modelDefaultAction = '';

    /**
     * Action for controlling the default model
     * @var string
     */
    public function getModelDefaultAction()
    {
        return $this->_modelDefaultAction;
    }

    /**
     * @param $modelDefaultAction
     * @return $this
     */
    public function setModelDefaultAction($modelDefaultAction)
    {
        $this->_modelDefaultAction = $modelDefaultAction;
        return $this;
    }


    /**
     * @var string
     */
    protected $_modelShowAttribute = '';

    /**
     * The attribute of the model to be shown in the bread crumbs, and the title of the page.
     * @var string
     */
    public function getModelShowAttribute()
    {
        return $this->_modelShowAttribute;
    }

    /**
     * @param $modelShowAttribute
     * @return $this
     */
    public function setModelShowAttribute($modelShowAttribute)
    {
        $this->_modelShowAttribute = $modelShowAttribute;
        return $this;
    }



    /**
     * @var string
     */
    protected $_modelPkAttribute = '';

    /**
     * PK will be used to find the model
     * @var string
     */
    public function getModelPkAttribute()
    {
        return $this->_modelPkAttribute;
    }

    /**
     * @param $modelPkAttribute
     * @return $this
     */
    public function setModelPkAttribute($modelPkAttribute)
    {
        $this->_modelPkAttribute = $modelPkAttribute;
        return $this;
    }


    /**
     * @var string
     */
    protected $_requestPkParamName = '';

    /**
     * The names of the parameter PK, in the query
     * @var string
     */
    public function getRequestPkParamName()
    {
        return $this->_requestPkParamName;
    }

    /**
     * @param $requestPkParamName
     * @return $this
     */
    public function setRequestPkParamName($requestPkParamName)
    {
        $this->_requestPkParamName = $requestPkParamName;
        return $this;
    }
}