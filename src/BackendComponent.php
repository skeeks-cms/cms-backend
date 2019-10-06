<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */

namespace skeeks\cms\backend;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @property BackendMenu $menu
 *
 * Class BackendComponent
 * @package skeeks\cms\backend
 */
class BackendComponent extends Component
    implements IBackendComponent, BootstrapInterface
{
    const EVENT_BEFORE_RUN = 'beforeRun';
    const EVENT_RUN = 'run';
    /**
     * @var BackendComponent
     */
    static protected $_runningBakcend = null;

    public $id;

    /**
     * @var array the list of IPs that are allowed to access this module.
     * Each array element represents a single IP filter which can be either an IP address
     * or an address with wildcard (e.g. 192.168.0.*) to represent a network segment.
     * The default value is `['127.0.0.1', '::1']`, which means the module can only be accessed
     * by localhost.
     */
    public $allowedIPs = ['*'];
    /**
     * @var string
     */
    public $controllerPrefix = "";
    /**
     * @var string
     */
    public $defaultRoute = "";
    /**
     * @var array
     */
    public $urlRule = [];
    /**
     * @var string
     */
    public $backendShowingControllerRoute = '/backend/admin-backend-showing';
    /**
     * @var bool
     */
    public $isMergeControllerMenu = false;

    /**
     * @var string
     */
    public $accessControl = AccessControl::class;

    /**
     * @var null
     */
    protected $_menu = null;
    /**
     * @var bool
     */
    protected $_isRunning = false;
    /**
     * @return null|BackendComponent
     */
    static public function getCurrent()
    {
        return static::$_runningBakcend;
    }
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->controllerPrefix) {
            throw new InvalidConfigException('Need controller prefix: '.static::class);
        }
    }

    /**
     * @param Application $application
     */
    public function bootstrap($application)
    {
        if ($application instanceof \yii\web\Application) {
            if ($this->_checkAccess()) {
                /**
                 * Adding routing rules
                 */
                $this->urlRule = ArrayHelper::merge([
                    'class' => 'skeeks\cms\backend\BackendUrlRule',
                ], $this->urlRule, [
                    'controllerPrefix' => $this->controllerPrefix,
                    'backendId'        => $this->id,
                ]);

                $application->urlManager->addRules([$this->urlRule]);
            }
        } else if ($application instanceof \yii\console\Application) {
            /**
             * Adding routing rules
             */
            $this->urlRule = ArrayHelper::merge([
                'class' => 'skeeks\cms\backend\BackendUrlRule',
            ], $this->urlRule, [
                'controllerPrefix' => $this->controllerPrefix,
                'backendId'        => $this->id,
            ]);

            $application->urlManager->addRules([$this->urlRule]);
        }
    }
    /**
     * @return boolean whether the module can be accessed by the current user
     */
    protected function _checkAccess()
    {
        if (\Yii::$app instanceof \yii\web\Application) {
            $ip = \Yii::$app->getRequest()->getUserIP();

            foreach ($this->allowedIPs as $filter) {
                if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                    return true;
                }
            }

            \Yii::warning('Access to Backend is denied due to IP address restriction. The requested IP is '.$ip, __METHOD__);

            return false;
        }

        return true;
    }
    /**
     * Run backend app
     * @return $this
     */
    public function run()
    {
        if (isset(\Yii::$app->cmsMarketplace) && \Yii::$app->cmsMarketplace) {
            \Yii::$app->cmsMarketplace->info;
        }

        if ($this->_isRunning === false && $this->_checkAccess()) {
            $this->_isRunning = true;
            static::$_runningBakcend = $this;

            $event = new Event();
            $this->trigger(self::EVENT_BEFORE_RUN, $event);

            $this->_run();

            $event = new Event();
            $this->trigger(self::EVENT_RUN, $event);
        }

        return $this;
    }
    protected function _run()
    {
        //Backend run code
    }
    /**
     * @return BackendMenu
     */
    public function getMenu()
    {
        if (is_array($this->_menu) || $this->_menu === null) {
            $data = (array)$this->_menu;
            if (!ArrayHelper::getValue($data, 'class')) {
                $data['class'] = BackendMenu::class;
            }

            if (!ArrayHelper::getValue($data, 'data')) {
                $data['data'] = [];
            }

            if ($this->isMergeControllerMenu) {
                $data['data'] = ArrayHelper::merge((array)$this->getMenuDataFromControllers(), (array)$data['data']);
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
        \Yii::beginProfile('getMenuDataFromControllers: '.$this->controllerPrefix);

        $result = [];

        foreach ($this->getCommands() as $route) {
            if ($class = \Yii::$app->createController($route)) {
                $class = $class[0];
                if (method_exists($class, 'getBackendMenuData')) {
                    $result = ArrayHelper::merge($result, $class->getBackendMenuData());
                }
            }
        }

        \Yii::endProfile('getMenuDataFromControllers: '.$this->controllerPrefix);

        return $result;
    }
    /**
     * Все контроллеры которыми оперирует данный backend компонент
     *
     * @return array
     */
    public function getCommands()
    {
        \Yii::beginProfile('getControllers: '.$this->controllerPrefix);

        $controllers = [];

        if ($allControllers = static::getAppCommands()) {
            foreach ($allControllers as $controllerRoute) {
                $controllerRoutes = explode('/', $controllerRoute);
                $controllerRouteLast = $controllerRoutes[count($controllerRoutes) - 1];

                $prefixs = explode("-", $controllerRouteLast);

                if (isset($prefixs[0]) && $prefixs[0] == $this->controllerPrefix) {
                    $controllers[] = $controllerRoute;
                }
            }
        }

        \Yii::endProfile('getControllers: '.$this->controllerPrefix);

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
        $prefix = $module instanceof Application ? '' : $module->getUniqueId().'/';

        $controllers = [];
        foreach (array_keys($module->controllerMap) as $id) {
            $controllers[] = $prefix.$id;
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
                    $controllerClass = $module->controllerNamespace.'\\'.substr(basename($file), 0, -4);
                    if (static::validateControllerClass($controllerClass)) {
                        $controllers[] = $prefix.Inflector::camel2id(substr(basename($file), 0, -14));
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