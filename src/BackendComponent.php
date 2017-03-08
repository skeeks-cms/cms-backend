<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */
namespace skeeks\cms\backend;

use yii\base\Application;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @property helpers\BackendMenu $menu
 *
 * Class BackendComponent
 * @package skeeks\cms\backend
 */
class BackendComponent extends Component
{
    /**
     * @var string
     */
    public $controllerPrefix    = "";

    /**
     * @var bool
     */
    public $isMergeControllerMenu = false;

    /**
     * @var null
     */
    protected $_menu = null;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->controllerPrefix)
        {
            throw new InvalidConfigException('Need controller prefix: ' . static::class);
        }
    }

    /**
     * @return BackendMenu
     */
    public function getMenu()
    {
        if (is_array($this->_menu))
        {
            $data = $this->_menu;
            if (!ArrayHelper::getValue($data, 'class'))
            {
                $data['class'] = BackendMenu::class;
            }

            if (!ArrayHelper::getValue($data, 'data'))
            {
                $data['data'] = [];
            }

            if ($this->isMergeControllerMenu)
            {
                $data['data'] = ArrayHelper::merge((array) $this->getMenuDataFromControllers(), (array) $data['data']);
            }

            $this->_menu = \Yii::createObject($data);
        }

        return $this->_menu;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setMenu($data = [])
    {
        $this->_menu = $data;
        return $this;
    }


    /**
     * Загрузка данных для меню из контроллеров
     *
     * @return array
     */
    public function getMenuDataFromControllers()
    {
        \Yii::beginProfile('getMenuDataFromControllers: ' . $this->controllerPrefix);

        $result = [];

        foreach ($this->getCommands() as $route)
        {
            if ($class = \Yii::$app->createController($route))
            {
                $class = $class[0];
                if (method_exists($class, 'getBackendMenuData'))
                {
                    $result = ArrayHelper::merge($result, $class->getBackendMenuData());
                }
            }
        }

        \Yii::endProfile('getMenuDataFromControllers: ' . $this->controllerPrefix);

        return $result;
    }


    /**
     * Все контроллеры которыми оперирует данный backend компонент
     *
     * @return array
     */
    public function getCommands()
    {
        \Yii::beginProfile('getControllers: ' . $this->controllerPrefix);

        $controllers = [];

        if ($allControllers = static::getAppCommands())
        {
            foreach ($allControllers as $controllerRoute)
            {
                $controllerRoutes = explode('/', $controllerRoute);
                $controllerRouteLast = $controllerRoutes[count($controllerRoutes) - 1];

                $prefixs = explode("-", $controllerRouteLast);

                if (isset($prefixs[0]) && $prefixs[0] == $this->controllerPrefix)
                {
                    $controllers[] = $controllerRoute;
                }
            }
        }

        \Yii::endProfile('getControllers: ' . $this->controllerPrefix);

        return $controllers;
    }

    /**
     * TODO:: add cache!
     *
     * Returns all available command names.
     * @return array all available command names
     */
    static public function getAppCommands()
    {
        \Yii::beginProfile('Scan all controllers');

        $commands = static::getModuleCommands(\Yii::$app);
        sort($commands);
        $result = array_unique($commands);

        \Yii::endProfile('Scan all controllers');

        return $result;
    }


    /**
     * Returns available commands of a specified module.
     * @param \yii\base\Module $module the module instance
     * @return array the available command names
     */
    static public function getModuleCommands($module)
    {
        $prefix = $module instanceof Application ? '' : $module->getUniqueId() . '/';

        $controllers = [];
        foreach (array_keys($module->controllerMap) as $id) {
            $controllers[] = $prefix . $id;
        }

        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) === null) {
                continue;
            }
            foreach (static::getModuleCommands($child) as $command) {
                $controllers[] = $command;
            }
        }

        $controllerPath = $module->getControllerPath();
        if (is_dir($controllerPath)) {
            $files = scandir($controllerPath);
            foreach ($files as $file) {
                if (!empty($file) && substr_compare($file, 'Controller.php', -14, 14) === 0) {
                    $controllerClass = $module->controllerNamespace . '\\' . substr(basename($file), 0, -4);
                    if (static::validateControllerClass($controllerClass)) {
                        $controllers[] = $prefix . Inflector::camel2id(substr(basename($file), 0, -14));
                        //$controllers[] = $controllerClass;
                    }
                }
            }
        }

        return $controllers;
    }

    /**
     * Validates if the given class is a valid console controller class.
     * @param string $controllerClass
     * @return bool
     */
    static public function validateControllerClass($controllerClass)
    {
        if (class_exists($controllerClass)) {
            $class = new \ReflectionClass($controllerClass);
            return !$class->isAbstract() && $class->isSubclassOf('yii\base\Controller');
        } else {
            return false;
        }
    }
}