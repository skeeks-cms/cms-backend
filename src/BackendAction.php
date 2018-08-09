<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend;

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\backend\models\BackendShowing;
use skeeks\cms\helpers\StringHelper;
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
use yii\base\Event;
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

    const EVENT_INIT = "init";

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

        $this->trigger(self::EVENT_INIT, new Event());
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









    public $backendShowingParam = 'sx-backend-showing';


    /**
     * @var BackendShowing
     */
    protected $_backendShowing = null;

    public function getBackendShowing()
    {
        if ($this->_backendShowing === null || !$this->_backendShowing instanceof BackendShowing) {
            //Find in get params
            if ($id = (int)\Yii::$app->request->get($this->backendShowingParam)) {
                if ($backendShowing = BackendShowing::findOne($id)) {
                    $this->_backendShowing = $backendShowing;
                    return $this->_backendShowing;
                } /*else {
                    \Yii::$app->response->redirect($this->indexUrl);
                    \Yii::$app->end();
                }*/
            } elseif ($id = (int)\Yii::$app->request->post($this->backendShowingParam)) {
                if ($backendShowing = BackendShowing::findOne($id)) {
                    $this->_backendShowing = $backendShowing;
                    return $this->_backendShowing;
                }
            }

            //Defauilt filter
            $backendShowing = BackendShowing::find()
                ->where(['key' => $this->uniqueId])
                //->andWhere(['cms_user_id' => \Yii::$app->user->id])
                ->andWhere(['is_default' => 1])
                ->one();

            if (!$backendShowing) {
                $backendShowing = new BackendShowing([
                    'key'        => $this->uniqueId,
                    //'cms_user_id' => \Yii::$app->user->id,
                    'is_default' => 1,
                ]);
                $backendShowing->loadDefaultValues();

                if ($backendShowing->save()) {

                } else {
                    throw new Exception('Backend showing not saved');
                }
            }

            $this->_backendShowing = $backendShowing;
        }

        return $this->_backendShowing;
    }


    /**
     * @param BackendShowing $backendShowing
     * @return string
     */
    public function getShowingUrl(BackendShowing $backendShowing)
    {
        $query = [];
        $url = $this->url;

        if ($pos = strpos($url, "?")) {
            $url = StringHelper::substr($url, 0, $pos);
            $stringQuery = StringHelper::substr($url, $pos + 1, StringHelper::strlen($url));
            parse_str($stringQuery, $query);
        }

        $query = [];

        $url = BackendUrlHelper::createByParams();
        if (is_array($this->urlData)) {
            $url->params = $this->urlData;
            $url->params[$this->backendShowingParam] = $backendShowing->id;
        } else {
            parse_str($this->urlData, $query);
            $url->params = $query;
            $url->params[$this->backendShowingParam] = $backendShowing->id;
        }


        $url->setBackendParamsByCurrentRequest();

        $query[$this->backendShowingParam] = $backendShowing->id;
        return $url->url;
    }

    /**
     * @return array|BackendShowing[]
     */
    public function getBackendShowings()
    {
        return BackendShowing::find()->where([
            'key' => $this->uniqueId,
        ])
            ->andWhere([
                'or',
                ['cms_user_id' => null],
                ['cms_user_id' => \Yii::$app->user->id],
            ])
            ->orderBy(['priority' => SORT_ASC])
            ->all();
    }
}