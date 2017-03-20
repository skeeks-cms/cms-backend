<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */
namespace skeeks\cms\backend\actions;
use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\backend\ViewBackendAction;
use skeeks\cms\rbac\CmsManager;
use yii\web\Application;

/**
 * @property IHasInfoActions|IBackendModelController    $controller
 * @property string $ownPermission;
 * @property string $permission;
 *
 * Class BackendModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendModelAction extends ViewBackendAction
    implements IBackendModelAction
{
    /**
     * @return string
     */
    public function getOwnPermission()
    {
        return $this->permission . '/own';
    }

    /**
     * @var string
     */
    protected $_permissionName = '';

    /**
     * @return string
     */
    public function getPermission()
    {
        if (!$this->_permissionName)
        {
            return $this->uniqueId;
        }

        return $this->_permissionName;
    }

    /**
     * @param $permissionName
     * @return $this
     */
    public function setPermission($permissionName)
    {
        $this->_permissionName = $permissionName;
        return $this;
    }


    public function init()
    {
        if ($this->permissionNames === null)
        {
            $this->permissionNames = [
                $this->permission       => $this->name,
                $this->ownPermission    => $this->name . " (только свои)",
            ];
        }

        parent::init();
    }


    protected function _initUrl()
    {
        if (!$this->_url)
        {
            if (!$this->controller->model)
            {
                return $this;
            }

            if ($this->controller->module instanceof Application)
            {
                $this->_url = [
                    '/' . $this->controller->id . '/' . $this->id,
                    $this->controller->requestPkParamName => $this->controller->modelPkValue
                ];
            } else
            {
                $this->_url = [
                    '/' . $this->controller->module->id . '/' . $this->controller->id . '/' . $this->id,
                    $this->controller->requestPkParamName => $this->controller->modelPkValue
                ];
            }
        }

        return $this;
    }


    protected function beforeRun()
    {
        if (parent::beforeRun())
        {
            if (!$this->controller->model)
            {
                $this->controller->redirect($this->controller->url);
                return false;
            }

            return true;
        }
    }

    public function run()
    {
        if ($this->callback)
        {
            return call_user_func($this->callback, $this);
        }

        if (!$this->controller->model)
        {
            return $this->controller->redirect($this->controller->url);
        }

        return parent::run();
    }

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        if (!parent::getIsVisible())
        {
            return false;
        }

        return $this->isAllow;
    }


    public function getIsAllow()
    {
        //Привилегия доступу к админке
        $permissionName = $this->getPermission();
        if (!$permission = \Yii::$app->authManager->getPermission($permissionName))
        {
            $permission = \Yii::$app->authManager->createPermission($permissionName);
            $permission->description = $this->name;
            \Yii::$app->authManager->add($permission);
        }

        if ($roleRoot = \Yii::$app->authManager->getRole(CmsManager::ROLE_ROOT))
        {
            if (!\Yii::$app->authManager->hasChild($roleRoot, $permission))
            {
                \Yii::$app->authManager->addChild($roleRoot, $permission);
            }
        }


        //Привилегия доступу к админке
        $permissionOwnName = $this->getOwnPermission();
        if (!$permissionOwn = \Yii::$app->authManager->getPermission($permissionOwnName))
        {
            $permissionOwn = \Yii::$app->authManager->createPermission($permissionOwnName);
            $permissionOwn->description = $this->name . ' (только свои записи)';
            $permissionOwn->ruleName = (new \skeeks\cms\rbac\AuthorRule())->name;
            \Yii::$app->authManager->add($permissionOwn);
        }

        if (!$permissionOwn->ruleName)
        {
            $permissionOwn->ruleName = (new \skeeks\cms\rbac\AuthorRule())->name;
            \Yii::$app->authManager->update($permissionOwn->name, $permissionOwn);
        }

        if ($roleRoot = \Yii::$app->authManager->getRole(CmsManager::ROLE_ROOT))
        {
            if (!\Yii::$app->authManager->hasChild($roleRoot, $permissionOwn))
            {
                \Yii::$app->authManager->addChild($roleRoot, $permissionOwn);
            }
        }

        if (!\Yii::$app->authManager->hasChild($permissionOwn, $permission))
        {
            \Yii::$app->authManager->addChild($permissionOwn, $permission);
        }


        foreach ([$this->getPermission() => $this->name] as $permissionName => $permissionLabel)
        {
            if (!\Yii::$app->user->can($permissionName, ['model' => $this->controller->model]))
            {
                return false;
            }
        }

        return true;
    }
}