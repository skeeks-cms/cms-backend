<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 06.03.2017
 */
namespace skeeks\cms\backend;

use skeeks\cms\backend\BackendUrlRule;
use skeeks\cms\backend\IHasBreadcrumbs;
use skeeks\cms\backend\IHasMenu;
use skeeks\cms\helpers\StringHelper;
use skeeks\cms\IHasInfo;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\rbac\CmsManager;
use skeeks\cms\traits\THasInfo;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property string $permissionName read-only;
 *
 * Class BackendController
 * @package skeeks\cms\backend
 */
abstract class BackendController extends Controller
    implements IHasPermissions, IHasInfo, IHasUrl, IHasInfoActions, IHasMenu, IHasBreadcrumbs
{
    use THasInfo;

    /**
     * @return array
     */
    public function getPermissionNames()
    {
        return [
            $this->permissionName
        ];
    }

    /**
     * The name of the privilege of access to this controller
     * @return string
     */
    public function getPermissionName()
    {
        return $this->getUniqueId();
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return
        [
            'access' =>
            [
                'class'         => AccessControl::className(),
                'rules' =>
                [
                    [
                        'allow'         => true,
                        'matchCallback' => function($rule, $action)
                        {
                            //Creating and Assigning Privileges for the Root User
                            if ($this->permissionNames)
                            {
                                foreach ($this->permissionNames as $permissionName)
                                {
                                    //Привилегия доступу к админке
                                    if (!$permission = \Yii::$app->authManager->getPermission($permissionName))
                                    {
                                        $permission = \Yii::$app->authManager->createPermission($permissionName);
                                        $permission->description = $this->name;
                                        \Yii::$app->authManager->add($permission);

                                        if ($roleRoot = \Yii::$app->authManager->getRole(CmsManager::ROLE_ROOT))
                                        {
                                            \Yii::$app->authManager->addChild($roleRoot, $permission);
                                        }
                                    }

                                    if (!\Yii::$app->user->can($permissionName))
                                    {
                                        return false;
                                    }
                                }

                                return true;
                            }

                            return false;
                        }
                    ],
                ]
            ],
        ];
    }

    public function init()
    {
        parent::init();

        if (!$this->name)
        {
            $this->name = Inflector::humanize(static::class);
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
        if (\Yii::$app->urlManager->rules)
        {
            foreach (\Yii::$app->urlManager->rules as $rule)
            {
                if ($rule instanceof BackendUrlRule)
                {
                    $request        = \Yii::$app->request;
                    $pathInfo       = $request->getPathInfo();
                    $params         = $request->getQueryParams();
                    $firstPrefix    = StringHelper::substr($pathInfo, 0, StringHelper::strlen($rule->urlPrefix));

                    if ($firstPrefix == $rule->urlPrefix)
                    {
                        return true;
                    }
                }
            }
        }

        throw new NotFoundHttpException("Request: " . \Yii::$app->request->pathInfo . " ip: " . \Yii::$app->request->userIP);
    }

    /**
     * @return $this
     */
    protected function _initMetaData()
    {
        $data = [];
        $data[] = \Yii::$app->name;
        $data[] = $this->name;

        if ($this->action && $this->action instanceof IHasInfo)
        {
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
            'url'   => $this->url
        ];

        if ($this->action && $this->action instanceof IHasInfo)
        {
             $result[] = $this->action->name;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $baseRoute = $this->module instanceof Application ? $this->id : ("/" . $this->module->id . "/" . $this->id);
        return Url::to([$baseRoute. '/' . $this->defaultAction]);
    }


    /**
     * @var Action[]
     */
    protected $_actions    = null;

    /**
     * @return BackendAction[]
     */
    public function getActions()
    {
        if ($this->_actions !== null)
        {
            return $this->_actions;
        }

        $actions = $this->actions();

        if ($actions)
        {
            foreach ($actions as $id => $data)
            {
                $action = $this->createAction($id);
                $this->_actions[$action->id] = $action;
            }
        } else
        {
            $this->_actions = [];
        }

        return $this->_actions;
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