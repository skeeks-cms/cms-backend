<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */
namespace skeeks\cms\backend;

use skeeks\cms\backend\BackendComponent;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\IHasInfo;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\traits\THasInfo;
use skeeks\cms\traits\THasPermissions;
use skeeks\cms\traits\THasUrl;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\ViewAction;
use \skeeks\cms\modules\admin\controllers\AdminController;

/**
 * @property IHasInfoActions    $controller
 *
 * Class AdminViewAction
 * @package skeeks\cms\modules\admin\actions
 */
class ViewBackendAction extends ViewAction
    implements IHasInfo, IHasUrl, IBackendAction, IHasPermissions
{
    use THasInfo;
    use THasUrl;
    use TBackendAction;
    use THasPermissions;


    /**
     * Do not use the default prefix.
     * @var string
     */
    public $viewPrefix = '';

    /**
     * @var string
     */
    public $defaultView = '';

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
        //Если название не задано, покажем что нибудь.
        if (!$this->name)
        {
            $this->name = Inflector::humanize($this->id);
        }

        if (!$this->controller instanceof IHasInfoActions)
        {
            throw new InvalidParamException( 'This action is designed to work with the controller: ' . IHasInfoActions::class);
        }

        if ($this->callback && !is_callable($this->callback))
        {
            throw new InvalidConfigException('"' . static::class . '::callback Should be a valid callback"');
        }

        if (!$this->defaultView)
        {
            $this->defaultView = $this->id;
        }


        if ($this->permissionNames === null)
        {
            $this->permissionNames = [
                $this->permissionName => $this->name
            ];
        }

        $this->_initUrl()->_initAccess();
        parent::init();
    }

    /**
     * @return $this|mixed
     */
    public function run()
    {
        if ($this->callback)
        {
            return call_user_func($this->callback, $this);
        }

        return parent::run();
    }
}