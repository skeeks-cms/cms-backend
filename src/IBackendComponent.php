<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 09.03.2017
 */
namespace skeeks\cms\backend;

/**
 * Interface IBackendComponent
 * @package skeeks\cms\backend
 */
interface IBackendComponent
{
    /**
     * @return BackendMenu
     */
    public function getMenu();

    /**
     * Start the component, load its settings
     * @return $this
     */
    public function run();
    
    /**
     * @return null|IBackendComponent
     */
    static public function getCurrent();
}