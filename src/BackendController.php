<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 06.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\helpers\StringHelper;
use skeeks\cms\IHasIcon;
use skeeks\cms\IHasImage;
use skeeks\cms\IHasName;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\traits\THasIcon;
use skeeks\cms\traits\THasImage;
use skeeks\cms\traits\THasName;
use skeeks\cms\traits\THasPermissions;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property BackendAction[] $allActions;
 *
 * Class BackendController
 * @package skeeks\cms\backend
 */
abstract class BackendController extends \skeeks\cms\base\Controller
    implements IHasPermissions, IHasName, IHasImage, IHasIcon, IHasUrl, IHasInfoActions, IHasMenu, IHasBreadcrumbs
{
    use THasName;
    use THasImage;
    use THasIcon;
    use THasPermissions;

    /**
     * Генерировать привелегии для доступа к действиям
     * @var bool
     */
    public $generateAccessActions = true;

    /**
     * @var array
     */
    public $actionsMap = [];
    /**
     * @var BackendAction[]
     */
    protected $_actions = null;
    /**
     * @var BackendAction[]
     */
    protected $_allActions = null;

    /**
     * @return array
     */
    public function behaviors()
    {
        $backend = BackendComponent::getCurrent();
        $class = $backend ? $backend->accessControl : AccessControl::class;

        return [
            'access' => [
                'class' => $class,
                'rules' => [
                    [
                        'allow'         => true,
                        'matchCallback' => function ($rule, $action) {
                            //Creating and Assigning Privileges for the Root User
                            return $this->isAllow;
                        },
                    ],
                ],
            ],
        ];
    }
    public function init()
    {
        parent::init();

        if ($this->name == '' && $this->name !== false) {
            $this->name = Inflector::humanize(static::class);
        }

        /**
         * По умолчанию главная привилегия заполняется значением полного роута контроллера
         */
        if ($this->permissionName === null) {
            $this->permissionName = $this->uniqueId;
        }

        /**
         * По умолчанию в привилегии попадает только главная привилегия
         */
        if ($this->permissionNames === null) {
            $this->permissionNames = [
                $this->permissionName => $this->name,
            ];
        }

    }
    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->_ensureUrl();
        $this->_initMetaData();

        return parent::beforeAction($action);
    }
    /**
     * Ensure prefix url
     *
     * @return bool
     * @throws NotFoundHttpException
     */
    protected function _ensureUrl()
    {
        /*if (!\Yii::$app->urlManager->enablePrettyUrl) {
            return true;
        }*/

        if (\Yii::$app->urlManager->rules) {
            foreach (\Yii::$app->urlManager->rules as $rule) {
                if ($rule instanceof BackendUrlRule) {
                    $request = \Yii::$app->request;
                    $pathInfo = $request->getPathInfo();
                    $params = $request->getQueryParams();

                    //Если например нужно на главной странице сделать бэкенд
                    if (!$pathInfo) {
                        return true;
                    }

                    $firstPrefix = StringHelper::substr($pathInfo, 0, StringHelper::strlen($rule->urlPrefix));

                    if ($firstPrefix == $rule->urlPrefix) {
                        return true;
                    }
                }
            }
        }

        throw new NotFoundHttpException("This is controller allow only with urlPrefix!");
    }
    /**
     * @return $this
     */
    protected function _initMetaData()
    {
        $data = [];
        $data[] = \Yii::$app->name;
        $data[] = $this->name;

        if ($this->action && $this->action instanceof IHasName) {
            $data[] = $this->action->name;
        }
        $this->view->title = implode(" / ", $data);
        return $this;
    }
    /**
     * @return array
     */
    public function getBreadcrumbsData()
    {
        $result = [];

        $result[] = [
            'label' => $this->name,
            'url'   => $this->url,
        ];

        if ($this->action && $this->action instanceof IHasName) {
            $result[] = $this->action->name;
        }

        return $result;
    }
    /**
     * @return string
     */
    public function getUrl()
    {
        $baseRoute = $this->module instanceof Application ? $this->id : ("/".$this->module->id."/".$this->id);
        return Url::to([$baseRoute.'/'.$this->defaultAction]);
    }
    /**
     * @return BackendAction[]
     */
    public function getActions()
    {
        if ($this->_actions !== null) {
            return $this->_actions;
        }

        $actions = $this->actions();

        if ($actions) {
            foreach ($actions as $id => $data) {
                $action = $this->createAction($id);
                $this->_actions[$action->id] = $action;
            }
        } else {
            $this->_actions = [];
        }

        return $this->_actions;
    }
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), $this->actionsMap);
    }
    /**
     * @return BackendAction[]
     */
    public function getAllActions()
    {
        if ($this->_allActions !== null) {
            return $this->_allActions;
        }

        $actions = $this->actions();

        if ($actions) {
            foreach ($actions as $id => $data) {
                $action = $this->createAction($id);
                $this->_allActions[$action->id] = $action;
            }
        } else {
            $this->_allActions = [];
        }

        return $this->_allActions;
    }

    /**
     *
     * @return array
     */
    public function getMenuData()
    {
        return [];
        /*[
            'admin' =>
            [
                'items' =>
                [
                    $this->uniqueId =>
                    [
                        'name'              => $this->name,
                        'url'               => $this->url,
                        'image'             => $this->image,
                        'icon'              => $this->icon,
                        'permissionNames'   => $this->permissionNames,
                    ]
                ]
            ]
        ];*/
    }
}