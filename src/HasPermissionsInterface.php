<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;

/**
 * @property $permissionNames;
 *
 * Interface HasPermissionsInterface
 * @package skeeks\cms\backend
 */
interface HasPermissionsInterface
{
    /**
     * Permission names
     * @return array
     */
    public function getPermissionNames();
}