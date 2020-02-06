<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\forms;

use skeeks\widget\chosen\Chosen;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
trait TActiveFormHasCustomSelect
{
    /**
     *
     * Стилизованный селект админки
     *
     * @param $model
     * @param $attribute
     * @param $items
     * @param array $config
     * @param array $fieldOptions
     * @return ActiveField
     */
    public function fieldSelect($model, $attribute, $items, $config = [], $fieldOptions = [])
    {
        $config = ArrayHelper::merge(
            ['allowDeselect' => false],
            $config,
            [
                'items' => $items,
            ]
        );

        foreach ($config as $key => $value) {
            if (property_exists(Chosen::className(), $key) === false) {
                unset($config[$key]);
            }
        }

        return $this->field($model, $attribute, $fieldOptions)->widget(
            Chosen::className(),
            $config
        );
    }
}