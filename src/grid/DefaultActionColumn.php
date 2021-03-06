<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 11.03.2018
 */

namespace skeeks\cms\backend\grid;

use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\modules\admin\widgets\ControllerModelActions;
use yii\grid\DataColumn;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class DefaultActionColumn extends DataColumn
{

    /**
     * @var bool
     */
    public $filter = false;

    /**
     * @var string
     */
    public $viewAttribute = "";

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $attribute = $this->viewAttribute ? $this->viewAttribute : $this->attribute;
        return \yii\helpers\Html::a($model->{$attribute}, "#", [
            'class' => "sx-trigger-action",
        ]);

    }
}