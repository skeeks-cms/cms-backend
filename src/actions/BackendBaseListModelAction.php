<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\backend\ViewBackendAction;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

/**
 * @property IHasInfoActions|IBackendModelController $controller
 * @property string                                  $modelClassName;
 * @property DataProviderInterface                                  $dataProvider;
 *
 * Class BackendModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendBaseListModelAction extends ViewBackendAction
{
    /**
     * @var null|string
     */
    public $_modelClassName = null;

    public $filters = [];
    public $sorts = [];

    /**
     * @return \skeeks\cms\backend\controllers\sting
     */
    public function getModelClassName()
    {
        if ($this->_modelClassName === null) {
            $this->_modelClassName = $this->controller->modelClassName;
        }

        return $this->_modelClassName;
    }

    /**
     * @param $className
     * @return $this
     */
    public function setModelClassName($className)
    {
        $this->_modelClassName = $className;

        return $this;
    }

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        return $this->controller->render('@skeeks/cms/backend/actions/views/model-list', [
            'model'      => $this->model,
            'formModels' => $this->formModels,
        ]);

        return parent::run();
    }



    /**
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        if ($this->_dataProvider === null) {
            $this->_dataProvider = $this->_createDataProvider();
        }

        return $this->_dataProvider;
    }

    /**
     * @return ActiveDataProvider
     */
    protected function _createDataProvider() {
        $modelClassName = $this->modelClassName;
        return new ActiveDataProvider([
            'query' => $modelClassName::find()
        ]);
    }

    protected $_dataProvider = null;
}