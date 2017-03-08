<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */
namespace skeeks\cms\backend;

use yii\base\Component;

/**
 * Class BackendComponent
 * @package skeeks\cms\backend
 */
class BackendComponent extends Component
{
    public $apps    = [
        'admin'     => [],
        'portal'    => []
    ];

    /**
     * TODO:: add cache!
     *
     * Possible paths of finding controllers
     * @return string[]
     */
    public function getControllerPaths()
    {
        $paths[] = \Yii::$app->controllerPath;

        foreach (\Yii::$app->getModules() as $key => $module)
        {
            $paths[] = \Yii::$app->getModule($key)->controllerPath;
        }

        return $paths;
    }
    /**
     * TODO:: add cache!
     *
     * Возможные пути находения контроллеров
     * @return string[]
     */
    public function getControllerNames()
    {
        \Yii::$app->module->controllerNamespace;
        return [];
    }
}