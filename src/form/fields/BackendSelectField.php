<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\form\fields;

use skeeks\cms\widgets\Select;
use skeeks\yii2\form\fields\SelectField;
use yii\helpers\ArrayHelper;

/**
 * Shared select field for backend controllers and customer cabinets.
 */
class BackendSelectField extends SelectField
{
    /** @var array */
    public $widgetConfig = [];

    public function getActiveField()
    {
        $field = parent::getActiveField();

        if ($this->multiple) {
            $this->elementOptions['multiple'] = $this->multiple;
        }

        if (!$this->multiple && !isset($this->elementOptions['size'])) {
            $this->elementOptions['size'] = 1;
        }

        $items = $this->getItems();
        ArrayHelper::remove($items, null);

        $field->widget(Select::class, ArrayHelper::merge([
            'items'    => $items,
            'multiple' => $this->multiple,
            'options'  => $this->elementOptions,
        ], $this->widgetConfig));

        return $field;
    }
}
