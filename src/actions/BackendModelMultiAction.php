<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\helpers\RequestResponse;
use skeeks\sx\helpers\ResponseHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class BackendModelMultiAction extends BackendAction
    implements IBackendModelMultiAction
{
    /**
     * @var array
     */
    public $options = [];

    public function init()
    {
        parent::init();

        $this->method   = 'post';
        $this->request  = 'ajax';
    }

    /**
     * @var array
     */
    public $models = [];

    /**
     * Обработчик каждогой модели
     * @var callable
     */
    public $eachCallback = null;

    public function run()
    {
        $rr = new ResponseHelper();

        $pk             = \Yii::$app->request->post($this->controller->requestPkParamName);
        $modelClass     = $this->controller->modelClassName;

        $this->models   = $modelClass::find()->where([
            $this->controller->modelPkAttribute => $pk
        ])->all();

        if (!$this->models)
        {
            $rr->success = false;
            $rr->message = \Yii::t('skeeks/cms', "No records found");
            return (array) $rr;
        }


        $data = [];
        foreach ($this->models as $model)
        {
            $raw = [];
            if ($this->eachExecute($model))
            {
                $data['success'] = ArrayHelper::getValue($data, 'success', 0) + 1;
            } else
            {
                $data['errors'] = ArrayHelper::getValue($data, 'errors', 0) + 1;
            }
        }

        $rr->success    = true;
        $rr->message    = \Yii::t('skeeks/cms',"Mission complete");
        $rr->data       = $data;
        return (array) $rr;
    }

    /**
     * @param $model
     * @return bool
     */
    public function eachExecute($model)
    {
        $action = null;

        if ($this->eachCallback && is_callable($this->eachCallback))
        {
            $callback = $this->eachCallback;
            return $callback($model, $action);
        }

        return true;
    }

    /**
     * @param GridView $grid
     * @return string
     */
    public function registerForGrid($grid)
    {
        $clientOptions = Json::encode($this->getClientOptions());

        $grid->view->registerJs(<<<JS
(function(sx, $, _)
{
    new sx.classes.grid.MultiAction(sx.Grid{$grid->id}, '{$this->id}' ,{$clientOptions});
})(sx, sx.$, sx._);
JS
);
        return "";
    }


    public function getClientOptions()
    {
        return [
            "id"                => $this->id,
            "url"               => (string) $this->url,
            "confirm"           => $this->confirm,
            "method"            => $this->method,
            "request"           => $this->request,
        ];
    }
}