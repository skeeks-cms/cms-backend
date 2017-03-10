<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 09.03.2017
 */
namespace skeeks\cms\backend;

/**
 * Presence of data for building a menu
 *
 * @property array $menuData;
 *
 * Interface IHasMenu
 * @package skeeks\cms\backend
 */
interface IHasMenu
{
    /**
     * @return array
     */
    public function getMenuData();
}