<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\rbac\CmsManager;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * @property IHasInfoActions|IBackendModelController $controller
 * @property string                                  $ownPermissionName;
 * @property string                                  $permissionName;
 * @property Model                                   $model;
 * @property string                                  $modelClassName;
 *
 * Class BackendModelAction
 * @package skeeks\cms\backend\actions
 */
trait TBackendModelAction
{
    /**
     * @return string
     */
    public function getOwnPermissionName()
    {
        return $this->permissionName.'/own';
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->controller->model;
    }

    /**
     * @return \skeeks\cms\backend\controllers\sting
     */
    public function getModelClassName()
    {
        return $this->controller->modelClassName;
    }


    public function init()
    {
        if ($this->permissionName === null && $this->accessCallback === null && $this->controller->generateAccessActions === true) {
            if ($this->controller->permissionName) {
                //Если у контроллера задана главная привилегия, то к ней добавляется текущий экшн, и эта строка становится главной привилегией текущего экшена
                $this->permissionName = $this->controller->permissionName."/".$this->id;
            } else {
                $this->permissionName = $this->uniqueId;
            }
        }

        if ($this->permissionNames === null && $this->accessCallback === null && $this->permissionName && $this->controller->generateAccessActions === true) {
            $this->permissionNames = [
                $this->permissionName => $this->name,
            ];

            $className = $this->modelClassName;
            $model = new $className();
            if (method_exists($model, 'hasAttribute') && $model->hasAttribute('created_by')) {
                $this->permissionNames = ArrayHelper::merge($this->permissionNames, [$this->ownPermissionName => $this->name." (".\Yii::t('skeeks/backend', 'Only your').")"]);
            }
        }

        parent::init();
    }


    protected function _initUrl()
    {
        if (!$this->_url) {
            if (!$this->model) {
                return $this;
            }

            if ($this->controller->module instanceof Application) {
                $this->_url = [
                    '/'.$this->controller->id.'/'.$this->id,
                    $this->controller->requestPkParamName => $this->controller->modelPkValue,
                ];
            } else {
                $this->_url = [
                    '/'.$this->controller->module->id.'/'.$this->controller->id.'/'.$this->id,
                    $this->controller->requestPkParamName => $this->controller->modelPkValue,
                ];
            }
        }

        return $this;
    }


    protected function beforeRun()
    {
        if (parent::beforeRun()) {
            if (!$this->model) {
                $this->controller->redirect($this->controller->url);
                return false;
            }

            return true;
        }
    }

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        if (!$this->model) {
            return $this->controller->redirect($this->controller->url);
        }

        return parent::run();
    }

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        if (!parent::getIsVisible()) {
            return false;
        }

        return $this->isAllow;
    }


    protected function _isAllow()
    {
        //Привилегия доступу к админке
        $permissionName = $this->permissionName;
        if ($permissionName) {
            if (!$permission = \Yii::$app->authManager->getPermission($permissionName)) {
                $permission = \Yii::$app->authManager->createPermission($permissionName);
                $permission->description = $this->name;
                \Yii::$app->authManager->add($permission);
            }

            if ($roleRoot = \Yii::$app->authManager->getRole(CmsManager::ROLE_ROOT)) {
                if (!\Yii::$app->authManager->hasChild($roleRoot, $permission)) {
                    \Yii::$app->authManager->addChild($roleRoot, $permission);
                }
            }


            $className = $this->modelClassName;
            $model = new $className();
            if (method_exists($model, 'hasAttribute') && $model->hasAttribute('created_by')) {
                $permissionOwnName = $this->ownPermissionName;
                if (!$permissionOwn = \Yii::$app->authManager->getPermission($permissionOwnName)) {
                    $permissionOwn = \Yii::$app->authManager->createPermission($permissionOwnName);
                    $permissionOwn->description = $this->name.' ('.\Yii::t('skeeks/backend', 'Only your').')';
                    $permissionOwn->ruleName = (new \skeeks\cms\rbac\AuthorRule())->name;
                    \Yii::$app->authManager->add($permissionOwn);
                }

                if (!$permissionOwn->ruleName) {
                    $permissionOwn->ruleName = (new \skeeks\cms\rbac\AuthorRule())->name;
                    \Yii::$app->authManager->update($permissionOwn->name, $permissionOwn);
                }
            }

            if (method_exists($model, 'hasAttribute') && $model->hasAttribute('created_by')) {
                if ($roleRoot = \Yii::$app->authManager->getRole(CmsManager::ROLE_ROOT)) {
                    if (!\Yii::$app->authManager->hasChild($roleRoot, $permissionOwn)) {
                        \Yii::$app->authManager->addChild($roleRoot, $permissionOwn);
                    }
                }

                if (!\Yii::$app->authManager->hasChild($permissionOwn, $permission)) {
                    \Yii::$app->authManager->addChild($permissionOwn, $permission);
                }
            }

            foreach ([$this->permissionName => $this->name] as $permissionName => $permissionLabel) {
                if (!\Yii::$app->user->can($permissionName, ['model' => $this->model])) {
                    return false;
                }
            }
        } else if ($this->permissionNames) {
            foreach ($this->permissionNames as $permissionName => $permissionLabel) {
                if (!\Yii::$app->user->can($permissionName, ['model' => $this->model])) {
                    return false;
                }
            }
        }

        return true;
        //return parent::_isAllow();
    }
}