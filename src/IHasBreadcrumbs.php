<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 09.03.2017
 */
namespace skeeks\cms\backend;

/**
 * Presence of data for building a breadcrumbs
 *
 * @property array $breadcrumbsData;
 *
 * Interface IHasBreadcrumbs
 * @package skeeks\cms\backend
 */
interface IHasBreadcrumbs
{
    /**
     * @return array
     */
    public function getBreadcrumbsData();
}