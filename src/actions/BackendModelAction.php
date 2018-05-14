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
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * @property IHasInfoActions|IBackendModelController    $controller
 * @property string $ownPermissionName;
 * @property string $permissionName;
 * @property Model $model;
 * @property string $modelClassName;
 *
 * Class BackendModelAction
 * @package skeeks\cms\backend\actions
 */
class BackendModelAction extends ViewBackendAction
    implements IBackendModelAction
{
    use TBackendModelAction;
}