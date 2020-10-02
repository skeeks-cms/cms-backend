<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\forms;

use skeeks\cms\widgets\Select;
use yii\helpers\ArrayHelper;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
trait TActiveFormHasCustomSelect
{
    /**
     *
     * Стилизованный селект админки
     *
     * @param       $model
     * @param       $attribute
     * @param       $items
     * @param array $config
     * @param array $fieldOptions
     * @return ActiveField
     */
    public function fieldSelect($model, $attribute, $items, $config = [], $fieldOptions = [])
    {
        $config = ArrayHelper::merge(
            //['allowDeselect' => false],
            $config,
            [
                'items' => $items,
            ]
        );

        /*foreach ($config as $key => $value) {
            if (property_exists(Select::class, $key) === false) {
                unset($config[$key]);
            }
        }*/
        return $this->field($model, $attribute, $fieldOptions)->widget(
            Select::class,
            $config
        );
    }

    /**
     * @param       $model
     * @param       $attribute
     * @param       $items
     * @param array $config
     * @param array $fieldOptions
     * @return ActiveField
     */
    public function fieldSelectMulti($model, $attribute, $items, $config = [], $fieldOptions = [])
    {
        $config = ArrayHelper::merge(
            $config, [
                'multiple' => true,
                //'size'     => 5,
            ]
        );
        return $this->fieldSelect($model, $attribute, $items, $config, $fieldOptions);
    }
}