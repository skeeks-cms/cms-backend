<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 11.03.2018
 */

namespace skeeks\cms\backend\grid;

use skeeks\cms\backend\BackendController;
use skeeks\cms\backend\controllers\BackendModelController;
use skeeks\cms\backend\widgets\AjaxControllerActionsWidget;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\modules\admin\widgets\ControllerModelActions;
use yii\base\InvalidConfigException;
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
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return \yii\helpers\Html::a($model->asText, "#", [
            'class' => "sx-trigger-action",
        ]);

    }
}