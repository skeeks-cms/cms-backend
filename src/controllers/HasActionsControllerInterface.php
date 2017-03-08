<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\controllers;
use yii\base\Action;

/**
 * @property Action[] $actions;
 *
 * Interface HasActionsControllerInterface
 * @package skeeks\cms\backend
 */
interface HasActionsControllerInterface
{
    /**
     * @return Action[]
     */
    public function getActions();
}