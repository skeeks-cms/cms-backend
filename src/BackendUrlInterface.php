<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;

/**
 * @property $url;
 *
 * Interface BackendUrlInterface
 * @package skeeks\cms\backend
 */
interface BackendUrlInterface
{
    /**
     * Has url
     * @return string
     */
    public function getUrl();
}