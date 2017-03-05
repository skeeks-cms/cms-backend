<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;

/**
 * Interface InfoInterface
 * @package skeeks\cms\backend
 */
interface InfoInterface
{
    /**
     * Controller name
     * @return string
     */
    public function getName();

    /**
     * Icon name
     * @return array
     */
    public function getIcon();

    /**
     * Image asset or url
     * @return array|string
     */
    public function getImage();
}