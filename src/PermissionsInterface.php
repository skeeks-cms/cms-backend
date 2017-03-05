<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;

/**
 * Interface PermissionsInterface
 * @package skeeks\cms\backend
 */
interface PermissionsInterface
{
    /**
     * Permission name
     * @return string
     */
    public function getPermissionNames();
}