<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\helpers\StringHelper;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\web\Application;

/**
 * @property IBackendComponent|BackendComponent $backend
 *
 * Class BackendUrlRule
 * @package skeeks\cms\backend
 */
class BackendUrlRule
    extends \yii\web\UrlRule
{
    /**
     * ~backend
     * @var string
     */
    public $urlPrefix = '';

    /**
     * backend
     * @var string
     */
    public $controllerPrefix = '';


    /**
     * BackendComponent id from config
     * @var string
     */
    public $backendId = '';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->name === null) {
            $this->name = __CLASS__;
        }

        if (!$this->urlPrefix) {
            throw new InvalidConfigException("Need 'urlPrefix'. Incorrect configuration of a component ".static::class);
        }

        if (!$this->backendId && $this->_backend === null) {
            throw new InvalidConfigException("Need 'backendId' or 'backend'. Incorrect configuration of a component ".static::class);
        }
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param string              $route
     * @param array               $params
     * @return bool|string
     */
    public function createUrl($manager, $route, $params)
    {
        if (\Yii::$app instanceof Application) {
            if ($systemParams = \Yii::$app->request->get(BackendUrlHelper::BACKEND_PARAM_NAME)) {
                $params = BackendUrlHelper::createByParams($params)->mergeBackendParamsByCurrentRequest()->params;
            }
        }


        $routeData = explode("/", $route);
        $isRoute = false;


        if ($routeData) {
            if (count($routeData) === 3) {
                $path = $routeData[1];
                $controllerPrefix = StringHelper::substr($path, 0, StringHelper::strlen($this->controllerPrefix));
                if ($this->controllerPrefix == $controllerPrefix) {
                    if ($path != $controllerPrefix) {
                        $isRoute = true;
                    } else {
                        $controllerData = \Yii::$app->createController($route);

                        if ($controllerData) {
                            $r = new \ReflectionClass($controllerData[0]);

                            $inflerctor = Inflector::camel2id($r->getShortName());
                            $inflerctorName = str_replace("-controller", "", $inflerctor);

                            if ($inflerctorName == $path) {
                                $isRoute = true;
                            }

                        }
                    }
                }
            } else {
                foreach ($routeData as $path) {
                    if (!$path) {
                        continue;
                    }

                    $controllerPrefix = StringHelper::substr($path, 0, StringHelper::strlen($this->controllerPrefix));
                    if ($this->controllerPrefix == $controllerPrefix) {
                        if ($path != $controllerPrefix) {
                            $isRoute = true;
                        } else {
                            $controllerData = \Yii::$app->createController($route);

                            if ($controllerData) {
                                $r = new \ReflectionClass($controllerData[0]);

                                $inflerctor = Inflector::camel2id($r->getShortName());
                                $inflerctorName = str_replace("-controller", "", $inflerctor);

                                if ($inflerctorName == $path) {
                                    $isRoute = true;
                                }

                            }
                        }
                    }
                }
            }


        }


        if ($isRoute === false) {
            return false;
        }

        $url = $this->urlPrefix."/".$route;

        /**
         * @see parent::createUrl()
         */
        if ($this->host !== null) {
            $pos = strpos($url, '/', 8);
            if ($pos !== false) {
                $url = substr($url, 0, $pos).preg_replace('#/+#', '/', substr($url, $pos));
            }
        } elseif (strpos($url, '//') !== false) {
            $url = preg_replace('#/+#', '/', $url);
        }

        /**
         * @see parent::createUrl()
         */
        if ($url !== '') {
            $url .= ($this->suffix === null ? $manager->suffix : $this->suffix);
        }

        /**
         * @see parent::createUrl()
         */
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '?'.$query;
        }


        return $url;
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param \yii\web\Request    $request
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        $params = $request->getQueryParams();
        $firstPrefix = substr($pathInfo, 0, strlen($this->urlPrefix));

        if ($firstPrefix == $this->urlPrefix) {
            if ($this->backend === null) {
                if ($this->backendId) {
                    throw new InvalidConfigException("Backend Id '{$this->backendId}' not exist. Incorrect configuration of a component ".static::class);
                } else {
                    throw new InvalidConfigException("Need 'backendId' or 'backend'. Incorrect configuration of a component ".static::class);
                }
            }

            $this->backend->run();
            $route = str_replace($this->urlPrefix, "", $pathInfo);
            if (!$route || $route == "/") {
                if ($this->backend->defaultRoute) {
                    $route = $this->backend->defaultRoute;
                } else {
                    //todo: может надо доработать
                    $route = "/".$this->controllerPrefix."/index";
                }


                /*print_r($route);
                die;*/
            }
            return [$route, $params];
        }

        return false;
    }


    /**
     * @var null|IBackendComponent|BackendComponent
     */
    protected $_backend = null;

    /**
     * @param IBackendComponent $backendComponent
     */
    public function setBackend(IBackendComponent $backendComponent)
    {
        $this->_backend = $backendComponent;
        return $this;
    }

    /**
     * @return null|BackendComponent|IBackendComponent
     */
    public function getBackend()
    {
        if ($this->_backend !== null) {
            return $this->_backend;
        }

        if ($this->backendId) {
            if (\Yii::$app->has($this->backendId)) {
                if ($backendComponent = \Yii::$app->get($this->backendId)) {
                    if ($backendComponent instanceof IBackendComponent) {
                        $this->_backend = $backendComponent;
                        return $this->_backend;
                    }
                }
            }
        }

        return null;
    }
}
