<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;
use yii\base\Action;

/**
 * @property BackendAction[] $actions;
 * @property BackendAction[] $allActions;
 *
 * Interface IHasInfoActions
 * @package skeeks\cms\backend
 */
interface IHasInfoActions
{
    /**
     * @return Action[]
     */
    public function getActions();

    /**
     * @return Action[]
     */
    public function getAllActions();
}