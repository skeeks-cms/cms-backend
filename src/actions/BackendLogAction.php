<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\IHasModel;
use skeeks\cms\IHasUrl;
use yii\base\DynamicModel;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * Class BackendModelCreateAction
 * @package skeeks\cms\backend\actions
 */
class BackendLogAction extends BackendModelAction
{
    public $defaultView = "@skeeks/cms/backend/actions/views/log";

    public function init()
    {
        if (!$this->icon) {
            $this->icon = "fa fa-list";
        }

        if (!$this->priority) {
            $this->priority = 500;
        }

        if (!$this->name) {
            $this->name = "Активность";
        }

        parent::init();
    }


    public function run()
    {

        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        return parent::run();
    }

}