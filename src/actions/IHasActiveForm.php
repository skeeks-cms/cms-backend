<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\actions;
use yii\widgets\ActiveForm;

/**
 * @property null|ActiveForm $activeForm read-only;
 * @property string $activeFormClassName;
 *
 * Interface IHasActiveForm
 *
 * @package skeeks\cms\backend\actions
 */
interface IHasActiveForm
{
    /**
     * @param array $config
     *
     * @return ActiveForm
     */
    public function beginActiveForm(array $config = []);

    /**
     * @return mixed
     */
    public function endActiveForm();

    /**
     * @return null|ActiveForm
     */
    public function getActiveForm();
}