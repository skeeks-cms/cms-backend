<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 18.08.2017
 */
namespace skeeks\cms\backend\helpers;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 *
 * @property [] backendParams
 *
 * @property bool isEmptyLayout
 * @property bool isNoActions
 * @property bool callbackEventName
 *
 * @property string url
 *
 * Class BackendParamsHelper
 * @package skeeks\cms\backend\helpers
 */
class BackendUrlHelper extends Component
{
    const BACKEND_PARAM_NAME = "_backend";

    const BACKEND_PARAM_NAME_EMPTY_LAYOUT               = "empty-layout";
    const BACKEND_PARAM_NAME_NO_ACTIONS                 = "no-actions";
    const BACKEND_PARAM_NAME_CALLBACK_EVENT_NAME        = "callback-event-name";

    /**
     * Url parametrs
     *
     * @var array
     */
    public $params = [];

    public function init()
    {
        if (!is_array($this->params))
        {
            throw new InvalidConfigException('!!!');
        }
    }

    /**
     * @param array $params
     * @return static
     */
    static public function createByParams($params = [])
    {
        return new static([
            'params' => $params
        ]);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function merge($params = [])
    {
        $this->params = ArrayHelper::merge($this->params, $params);
        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
    public function setBackendParams($data = [])
    {
        $params = $this->params;
        $params[static::BACKEND_PARAM_NAME] = $data;
        $this->params = $params;

        return $this;
    }

    /**
     * @return $this
     */
    public function setBackendParamsByCurrentRequest()
    {
        if ($systemParams = \Yii::$app->request->get(static::BACKEND_PARAM_NAME))
        {
            $this->setBackendParams($systemParams);
        }

        return $this;
    }
    /**
     * @return $this
     */
    public function mergeBackendParamsByCurrentRequest()
    {
        if ($systemParams = \Yii::$app->request->get(static::BACKEND_PARAM_NAME))
        {
            if ($this->getBackendParams()) {
                $this->setBackendParams(ArrayHelper::merge($systemParams, $this->getBackendParams()));
            } else {
                $this->setBackendParams($systemParams);
            }

        }

        return $this;
    }

    /**
     * @return array
     */
    public function getBackendParams()
    {
        return (array) ArrayHelper::getValue($this->params, static::BACKEND_PARAM_NAME);
    }

    /**
     * @param array $data
     * @return array
     */
    public function setBackendParam($paramName, $paramValue)
    {
        $params = $this->backendParams;
        $params[$paramName] = $paramValue;
        $this->setBackendParams($params);

        return $this;
    }

    /**
     * @param $paramName
     * @param null $default
     * @return mixed
     */
    public function getBackenParam($paramName, $default = null)
    {
        $params = $this->getBackendParams();
        return ArrayHelper::getValue($params, $paramName, $default);
    }


    /**
     * @return $this
     */
    public function enableEmptyLayout()
    {
        $this->setBackendParam(static::BACKEND_PARAM_NAME_EMPTY_LAYOUT, true);
        return $this;
    }

    /**
     * @return $this
     */
    public function enableNoActions()
    {
        $this->setBackendParam(static::BACKEND_PARAM_NAME_NO_ACTIONS, true);
        return $this;
    }

    /**
     * @param $eventName
     * @return $this
     */
    public function setCallbackEventName($eventName)
    {
        $this->setBackendParam(static::BACKEND_PARAM_NAME_CALLBACK_EVENT_NAME, $eventName);
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEmptyLayout()
    {
        return (bool) $this->getBackenParam(static::BACKEND_PARAM_NAME_EMPTY_LAYOUT, false);
    }

    /**
     * @return bool
     */
    public function getIsNoActions()
    {
        return (bool) $this->getBackenParam(static::BACKEND_PARAM_NAME_NO_ACTIONS, false);
    }

    /**
     * @return string
     */
    public function getCallbackEventName()
    {
        return (string) $this->getBackenParam(static::BACKEND_PARAM_NAME_CALLBACK_EVENT_NAME);
    }

    /**
     * @param bool $scheme
     * @return string
     */
    public function getUrl($scheme = false)
    {
        return Url::to($this->params, $scheme);
    }
}