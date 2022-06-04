<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\forms;

use skeeks\cms\backend\widgets\FormButtonsBackendWidget;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
trait TActiveFormHasButtons
{
    /**
     * @var string
     */
    public $buttonsClass = FormButtonsBackendWidget::class;

    /**
     * @var array
     */
    public $buttonsConfig = [];

    /**
     * @param Model $model
     * @param array $buttons - param is deprecated
     * @return string
     */
    public function buttonsStandart(Model $model, $buttons = ['apply'], $successMessage = '')
    {
        $widgetClass = $this->buttonsClass;

        if (in_array("apply", $buttons)) {
            $this->buttonsConfig['isSave'] = true;
        } else {
            $this->buttonsConfig['isSave'] = false;
        }

        if (in_array("save", $buttons)) {
            $this->buttonsConfig['isSaveAndClose'] = true;
        } else {
            $this->buttonsConfig['isSaveAndClose'] = false;
        }

        if (in_array("close", $buttons)) {
            $this->buttonsConfig['isClose'] = true;
        } else {
            $this->buttonsConfig['isClose'] = false;
        }
        
        $widgetConfig = ArrayHelper::merge($this->buttonsConfig, [
            'activeForm' => $this,
            'successMessage'      => $successMessage,
            'model'      => $model,
        ]);

        return $widgetClass::widget($widgetConfig);
    }
}