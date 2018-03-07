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
 *
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
    protected $_grid = [];

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        return $this->controller->render('@skeeks/cms/backend/actions/views/grid');

        return parent::run();
    }

    public function getGrid()
    {
        return $this->_grid;
    }

    /**
     * @param array $gridData
     * @return $this
     */
    public function setGrid($gridData = [])
    {
        if ($this->_grid === null) {
            $this->_grid = $gridData;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getGridClassName()
    {
        return (string) ArrayHelper::getValue($this->grid, 'class', GridViewWidget::class);
    }

    /**
     * @return []
     */
    public function getGridConfig()
    {
        $gridConfig = ArrayHelper::getValue($this->grid, 'class');
        ArrayHelper::remove($gridConfig, 'class');
        $gridConfig['columns'] = $this->columns;
        $gridConfig['dataProvider'] = $this->dataProvider;

        return (array) $gridConfig;
    }

    public $enabledColumns = [];

    public function getColumns()
    {
        $columns = [];
        $modelClassName = $this->modelClassName;
        $model = new $modelClassName();
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $columns[] = (string) $name;
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