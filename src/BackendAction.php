<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\IHasIcon;
use skeeks\cms\IHasImage;
use skeeks\cms\IHasName;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\modules\admin\widgets\ControllerActions;
use skeeks\cms\traits\THasIcon;
use skeeks\cms\traits\THasImage;
use skeeks\cms\traits\THasName;
use skeeks\cms\traits\THasPermissions;
use skeeks\cms\traits\THasUrl;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\helpers\Inflector;

/**
 * @property IHasInfoActions $controller
 *
 * Class AdminViewAction
 * @package skeeks\cms\modules\admin\actions
 */
class BackendAction extends Action
    implements IHasName, IHasIcon, IHasImage, IHasUrl, IBackendAction, IHasPermissions
{
    use THasName;
    use THasImage;
    use THasIcon;
    use THasUrl;
    use TBackendAction;
    use THasPermissions;

    /**
     * @return string
     */
    public function getPermissionName()
    {
        if ($this->_permissionName !== false) {
            return $this->controller->permissionName."/".$this->id;
        }

        return $this->_permissionName;
    }

    public function init()
    {
        //Если название не задано, покажем что нибудь.
        if (!$this->name) {
            $this->name = Inflector::humanize($this->id);
        }

        if (!$this->controller instanceof IHasInfoActions) {
            throw new InvalidParamException('This action is designed to work with the controller: '.IHasInfoActions::class);
        }

        if ($this->callback && !is_callable($this->callback)) {
            throw new InvalidConfigException('"'.static::class.'::callback Should be a valid callback"');
        }

        if ($this->permissionNames === null) {
            $this->permissionNames = [
                $this->uniqueId => $this->name,
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
        if ($this->callback) {
            $result = call_user_func($this->callback, $this);
        } else {
            $result = '';
            //$result = parent::run();
        }

        return $result;
    }
}