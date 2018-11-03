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
class BackendModelMultiDeleteAction extends BackendModelMultiAction
{
    public function init()
    {
        if (!$this->icon)
        {
            $this->icon = "fa fa-trash";
        }

        if (!$this->confirm)
        {
            $this->confirm = \Yii::t('skeeks/backend', 'Are you sure you want to permanently delete the selected items?');
        }

        if (!$this->name)
        {
            $this->name = \Yii::t('skeeks/backend', "Delete");
        }

        if (!$this->priority)
        {
            $this->priority = 99999;
        }

        parent::init();
    }

    /**
     * @param $model
     * @return bool
     */
    public function eachExecute($model)
    {
        try
        {
            return $model->delete();
        } catch (\Exception $e)
        {
            return false;
        }
    }

}