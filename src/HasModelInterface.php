<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;
use yii\base\Model;

/**
 * @property Model $model;
 *
 * Interface HasModelInterface
 * @package skeeks\cms\backend
 */
interface HasModelInterface
{
    /**
     * @return Model
     */
    public function getModel();
}