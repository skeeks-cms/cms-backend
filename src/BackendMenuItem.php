<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */
namespace skeeks\cms\backend;

use skeeks\cms\backend\controllers\BackendController;
use skeeks\cms\IHasInfo;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\traits\THasInfo;
use skeeks\cms\traits\THasPermissions;
use skeeks\cms\traits\THasUrl;
use yii\base\Component;

/**
 * @property bool $isVisible
 * @property bool $isActive
 * @property bool $isAllow
 *
 * Class BackendMenuItem
 * @package skeeks\cms\backend
 */
class BackendMenuItem extends Component
    implements IHasInfo, IHasUrl, IHasPermissions
{
    use THasInfo;
    use THasUrl;
    use THasPermissions;

    /**
     * @var
     */
    public $id;

    /**
     * @var int
     */
    public $priority    = 100;

    /**
     * @var bool
     */
    public $visible     = true;

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
     * @return bool
     */
    public function init()
    {
        parent::init();

        $controller = null;
        //Default access rights
        if (!$this->permissionNames && is_array($this->url))
        {
            if ($controller = $this->_getController())
            {
                if ($controller instanceof IHasPermissions)
                {
                    $this->permissionNames = $controller->permissionNames;
                }
            }

        }

        //No name specified
        if (!$this->name)
        {
            if ($controller = $this->_getController())
            {
                if ($controller instanceof IHasInfo)
                {
                    $this->name = $controller->name;
                }
            }
        }
    }

    /**
     * @var null|BackendController
     */
    protected $_controller = null;

    /**
     * @return BackendController
     */
    protected function _getController()
    {
        if ($this->_controller !== null)
        {
            return $this->_controller;
        }

        try
        {
            list($controller, $route) = \Yii::$app->createController($this->url[0]);
            $this->_controller = $controller;
        } catch (\Exception $e)
        {
            $this->_controller = false;
        }

        return $this->_controller;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getImage()
    {
        if ($this->_image === null)
        {
            return "";
        } if (is_array($this->_image) && count($this->_image) == 2)
        {
            list($assetClassName, $localPath) = $this->_image;
            return (string) \Yii::$app->getAssetManager()->getAssetUrl(\Yii::$app->assetManager->getBundle($assetClassName), $localPath);
        } if (is_string($image))
        {
            return $this->_image;
        }

        return "";
    }

    /**
     * @return bool
     */
    public function getIsVisbile()
    {
        if ($this->visible === true && $this->isAllow)
        {
            if ($this->items)
            {
                foreach ($this->items as $item)
                {
                    if ($item->isVisible)
                    {
                        return true;
                    }
                }

                return false;
            } else
            {
                return true;
            }
        }


        return false;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        if ($this->items)
        {
            foreach ($this->items as $item)
            {
                if ($item->isActive)
                {
                    return true;
                }
            }
        }

        if ($this->activeCallback && is_callable($this->activeCallback))
        {
            $callback = $this->activeCallback;
            return (bool) $callback($this);
        }

        return false;
    }

    /**
     * @param $user
     * @return bool
     */
    public function getIsAllow()
    {
        if ($this->permissionNames)
        {
            foreach ($this->permissionNames as $permissionName)
            {
                if ($permission = \Yii::$app->authManager->getPermission($permissionName))
                {
                    if (\Yii::$app->user->can($permission->name))
                    {
                        return $this->_accessCallback();
                    } else
                    {
                        return false;
                    }
                } else
                {
                    return false;
                }
            }
        }


        return $this->_accessCallback();
    }

    /**
     * @return bool
     */
    protected function _accessCallback()
    {
        if ($this->accessCallback && is_callable($this->accessCallback))
        {
            $callback = $this->accessCallback;
            return (bool) $callback($this);
        }

        return true;
    }






}