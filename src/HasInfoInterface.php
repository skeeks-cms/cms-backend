<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;

/**
 * @property $name;
 * @property $icon;
 * @property $image;
 *
 * Interface HasInfoInterface
 * @package skeeks\cms\backend
 */
interface HasInfoInterface
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