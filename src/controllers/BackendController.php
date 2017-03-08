<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 06.03.2017
 */
namespace skeeks\cms\backend\controllers;

use skeeks\cms\backend\BackendUrlRule;
use skeeks\cms\helpers\StringHelper;
use skeeks\cms\IHasInfo;
use skeeks\cms\IHasPermissions;
use skeeks\cms\IHasUrl;
use skeeks\cms\traits\THasInfo;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class BackendController
 * @package skeeks\cms\backend\controllers
 */
class BackendController extends Controller
    implements IHasPermissions, IHasInfo, IHasUrl, IHasInfoActions
{
    use THasInfo;

    /**
     * @return array
     */
    public function getPermissionNames()
    {
        return [
            $this->getUniqueId()
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return
        [
            //Проверка основной привелигии доступа к админ панели
            'access' =>
            [
                'class'         => AccessControl::className(),
                'rules' =>
                [
                    [
                        'allow'         => true,
                        'roles'         => $this->permissionNames,
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
        $this->_initBreadcrumbsData();

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

        if ($this->action && $this->action instanceof HasInfoInterface)
        {
            $data[] = $this->action->name;
        }
        $this->view->title = implode(" / ", $data);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _initBreadcrumbsData()
    {
        $this->view->params['breadcrumbs'][] = [
            'label' => $this->name,
            'url' => $this->url
        ];

        if ($this->action && $this->action instanceof HasInfoInterface)
        {
             $this->view->params['breadcrumbs'][] = $this->action->name;
        }

        return $this;
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
     * @return Action[]
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
            }
        } else
        {
            $this->_actions = [];
        }

        return $this->_actions;
    }


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