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
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * @property IHasInfoActions|IBackendModelController    $controller
 * @property string $ownPermissionName;
 * @property string $permissionName;
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
    public function getOwnPermissionName()
    {
        return $this->permissionName . '/own';
    }

    /**
     * @return string
     */
    public function getPermissionName()
    {
        if ($this->_permissionName !== false)
        {
            return $this->controller->permissionName . "/" . $this->id;
        }

        return $this->_permissionName;
    }


    public function init()
    {
        if ($this->permissionNames === null)
        {
            $this->permissionNames = [
                $this->permissionName       => $this->name,
            ];

            $className = $this->controller->modelClassName;
            $model = new $className();
            if (method_exists($model, 'hasAttribute') && $model->hasAttribute('created_by'))
            {
                $this->permissionNames = ArrayHelper::merge($this->permissionNames, [$this->ownPermissionName =>  $this->name . " (" . \Yii::t('skeeks/backend', 'Only your') . ")"]);
            }
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
        $permissionName = $this->permissionName;
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


        $className = $this->controller->modelClassName;
        $model = new $className();
        if (method_exists($model, 'hasAttribute') && $model->hasAttribute('created_by'))
        {
            $permissionOwnName = $this->ownPermissionName;
            if (!$permissionOwn = \Yii::$app->authManager->getPermission($permissionOwnName))
            {
                $permissionOwn = \Yii::$app->authManager->createPermission($permissionOwnName);
                $permissionOwn->description = $this->name . ' (' . \Yii::t('skeeks/backend', 'Only your') . ')';
                $permissionOwn->ruleName = (new \skeeks\cms\rbac\AuthorRule())->name;
                \Yii::$app->authManager->add($permissionOwn);
            }

            if (!$permissionOwn->ruleName)
            {
                $permissionOwn->ruleName = (new \skeeks\cms\rbac\AuthorRule())->name;
                \Yii::$app->authManager->update($permissionOwn->name, $permissionOwn);
            }
        }



        if (method_exists($model, 'hasAttribute') && $model->hasAttribute('created_by'))
        {
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
        }

        foreach ([$this->permissionName => $this->name] as $permissionName => $permissionLabel)
        {
            if (!\Yii::$app->user->can($permissionName, ['model' => $this->controller->model]))
            {
                return false;
            }
        }

        return true;
    }
}