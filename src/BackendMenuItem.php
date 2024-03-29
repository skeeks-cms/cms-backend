<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\IHasIcon;
use skeeks\cms\IHasImage;
use skeeks\cms\IHasName;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\traits\THasIcon;
use skeeks\cms\traits\THasImage;
use skeeks\cms\traits\THasName;
use skeeks\cms\traits\THasPermission;
use skeeks\cms\traits\THasPermissions;
use skeeks\cms\traits\THasUrl;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property bool        $isVisible
 * @property bool        $isActive
 * @property bool        $isAllow
 *
 * @property array|mixed $urlData
 *
 * Class BackendMenuItem
 * @package skeeks\cms\backend
 */
class BackendMenuItem extends Component
    implements IHasName, IHasImage, IHasIcon, IHasUrl, IHasPermissions
{
    use THasName;
    use THasImage;
    use THasIcon;
    use THasUrl;
    use THasPermission;
    use THasPermissions;

    /**
     * @var
     */
    public $id;

    /**
     * @var int
     */
    public $priority = 100;

    /**
     * @var bool
     */
    public $visible = true;

    /**
     * @var static[]
     */
    public $items = [];

    /**
     * @var BackendMenu
     */
    public $menu = null;

    /**
     * @var static
     */
    public $parent = null;


    /**
     * @var callable
     */
    public $activeCallback = null;
    /**
     * @var callable
     */
    public $accessCallback = null;
    /**
     * @var null|\skeeks\cms\backend\BackendController
     */
    protected $_controller = null;
    /**
     * @return bool
     */
    public function init()
    {
        parent::init();

        $controller = null;
        //Default access rights

        /*if (!$this->permissionNames && is_array($this->_url)) {
            if ($controller = $this->_getController()) {
                if ($controller instanceof IHasPermissions) {
                    $this->permissionNames = $controller->permissionNames;
                }
            }
        }*/

        //No name specified
        if (!$this->name) {
            if ($controller = $this->_getController()) {
                if ($controller instanceof IHasName) {
                    $this->name = $controller->name;
                }
            }
        }
    }
    /**
     * @return \skeeks\cms\backend\BackendController
     */
    protected function _getController()
    {
        if ($this->_controller !== null) {
            return $this->_controller;
        }

        try {
            list($controller, $route) = \Yii::$app->createController($this->_url[0]);
            $this->_controller = $controller;
        } catch (\Exception $e) {
            $this->_controller = false;
        }

        return $this->_controller;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        if (is_array($this->_url)) {
            if (isset($this->_url[0])) {
                $this->_url[0] = "/".$this->_url[0];
            }

            return Url::to($this->_url);

        } else if (is_string($this->_url)) {
            return $this->_url;
        } else {
            if ($this->items) {
                foreach ($this->items as $childItem)
                {
                    if ($childItem->url) {
                        return $childItem->url;
                    }
                }
                /*$item = ArrayHelper::getValue($this->items, 0);
                if ($item) {
                    return $item->url;
                }*/
            }
        }

        return "";
    }

    /**
     * @return mixed
     */
    public function getUrlData()
    {
        return $this->_url;
    }

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        if ($this->visible === true) {
            if ($this->items) {
                foreach ($this->items as $item) {
                    if ($item->isVisible) {
                        return true;
                    }
                }

                return false;
            } else {
                if ($this->_url) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        if ($this->items) {
            foreach ($this->items as $item) {
                if ($item->isActive) {
                    return true;
                }
            }
        }

        if ($this->activeCallback && is_callable($this->activeCallback)) {
            $callback = $this->activeCallback;
            return (bool)call_user_func($callback, $this);
        }

        if (is_array($this->_url)) {
            $routeData = explode("/", $this->_url[0]);
            $routeDataCheck = [];
            if ($routeData && is_array($routeData)) {
                foreach ($routeData as $routePath) {
                    if ($routePath) {
                        $routeDataCheck[] = $routePath;
                    }
                }
            }

            if (!$routeDataCheck) {
                return false;
            }

            if (strpos('-'.\Yii::$app->controller->route.'/', implode("/", $routeDataCheck).'/') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $user
     * @return bool
     */
    public function getIsAllow()
    {
        if ($this->accessCallback && is_callable($this->accessCallback)) {
            $callback = $this->accessCallback;
            return (bool)call_user_func($callback, $this);
        }

        return $this->_isAllow();
    }

    /**
     * @return bool
     */
    protected function _isAllow()
    {
        if ($this->_getController() && $this->_getController() instanceof IHasPermissions && !$this->_getController()->isAllow) {
            return false;
        }

        /*if ($this->permissionNames) {
            foreach ($this->permissionNames as $permissionName => $permissionLabel) {
                if ($permission = \Yii::$app->authManager->getPermission($permissionName)) {
                    if (!\Yii::$app->user->can($permission->name)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }*/

        return true;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setLabel($name)
    {
        $this->setName($name);
        return $this;
    }
    /**
     * @param $name
     * @return $this
     */
    public function setImg($imgData)
    {
        $this->setImage($imgData);
        return $this;
    }
}