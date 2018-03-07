<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\widgets\GridViewWidget;
use yii\helpers\ArrayHelper;
/**
 *
 * @property [] $columns
 * @property [] $grid
 * @property string $gridClassName
 * @property [] $gridClassConfig
 *
 * ***
 *
 * Class BackendGridModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendGridModelAction extends BackendBaseListModelAction
{
    /**
     * @var array
     */
    public $gridConfig = [];

    /**
     * @var string
     */
    public $gridClassName;

    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var array
     */
    public $anabledColumns = [];

    public function init()
    {
        if (!$this->gridClassName) {
            $this->gridClassName = GridViewWidget::class;
        }
    }


    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        $this->gridConfig['columns'] = $this->columns;
        $this->gridConfig['dataProvider'] = $this->dataProvider;

        return $this->controller->render('@skeeks/cms/backend/actions/views/grid');
    }


    public $defaultEnabledColumns = [];

    public function getColumns()
    {
        $columns = [];
        $modelClassName = $this->modelClassName;
        $model = new $modelClassName();
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $this->columns[] = (string) $name;
                }
            }
        }

        print_r($columns);die;
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        $models = $this->dataProvider->getModels();
        $model = reset($models);
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $this->columns[] = (string) $name;
                }
            }
        }
    }


}