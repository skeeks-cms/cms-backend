<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\controllers;
use skeeks\cms\backend\actions\BackendModelAction;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * @property sting $modelClassName;
 * @property sting $modelDefaultAction;
 * @property sting $modelShowAttribute;
 * @property sting $modelShowName;
 * @property sting $modelPkAttribute;
 * @property sting $requestPkParamName;
 * @property BackendModelAction[] $modelActions;
 *
 * Interface BackendModelControllerInterface
 * @package skeeks\cms\backend
 */
interface IBackendModelController
{
    /**
     * Required to fill!
     * The model class with which the controller operates.
     *
     * @example ActiveRecord::class;
     * @var string
     */
    public function getModelClassName();

    /**
     * Action for controlling the default model
     * @var string
     */
    public function getModelDefaultAction();

    /**
     * The attribute of the model to be shown in the bread crumbs, and the title of the page.
     * @var string
     */
    public function getModelShowAttribute();

    /**
     * PK will be used to find the model
     * @var string
     */
    public function getModelPkAttribute();

    /**
     * The names of the parameter PK, in the query
     * @var string
     */
    public function getRequestPkParamName();


    /**
     * @return string
     */
    public function getModelShowName();

    /**
     * @return string
     */
    public function getModelPkValue();

    /**
     * @return BackendModelAction[]
     */
    public function getModelActions();
}