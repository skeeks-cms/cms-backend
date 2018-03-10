<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\backend\widgets\GridViewWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use yii\helpers\ArrayHelper;
/**
 * @property string $gridClassName
 * @property [] $gridConfig
 *
 * ***
 *
 * Class BackendGridModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendGridModelAction extends BackendAction
{
    public $gridClassName;
    public $gridConfig = [];

    protected $_modelClassName;

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return (string)$this->controller->modelClassName;
    }

    public function init()
    {
        if (!$this->gridClassName) {
            $this->gridClassName = GridViewCmsWidget::class;
        }

        $this->gridConfig['modelClassName'] = $this->modelClassName;

        if (!isset($this->gridConfig['gridClassName'])) {
            $this->gridConfig['gridClassName'] = GridViewWidget::class;
        }

        if (isset($this->gridConfig['columns'])) {
            $this->gridConfig['columns'] = ArrayHelper::merge([
                'checkbox' => [
                    'class' => 'skeeks\cms\grid\CheckboxColumn'
                ],
                /*'actions' => [
                    'class'                 => \skeeks\cms\modules\admin\grid\ActionColumn::class,
                    'controller'            => $this->controller,
                    'isOpenNewWindow'       => true
                ]*/
            ], $this->gridConfig['columns']);
        }

        parent::init();
    }

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        return $this->controller->render('@skeeks/cms/backend/actions/views/grid');
    }
}