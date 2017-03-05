<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend;
use \yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class BackendUrlRule
 *
 * @package skeeks\cms\backend
 */
class BackendUrlRule
    extends \yii\web\UrlRule
{
    /**
     * @var string
     */
    public $urlPrefix = '~backend';

    /**
     * @var string
     */
    public $routePrefix = 'backend';


    public function init()
    {
        if ($this->name === null)
        {
            $this->name = __CLASS__;
        }

        if (!$this->urlPrefix)
        {
            throw new InvalidConfigException("Incorrect configuration of a component " . static::class);
        }
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param string $route
     * @param array $params
     * @return bool|string
     */
    public function createUrl($manager, $route, $params)
    {
        $routeData = explode("/", $route);
        $isAdminRoute = false;
        if (isset($routeData[1]))
        {
            if (strpos(trim($routeData[1]), 'admin-') == 0)
            {
                $isAdminRoute = true;
            }
        }

        if ($isAdminRoute === false)
        {
            return false;
        }

        $url = $this->urlPrefix . "/" . $route;
        if (!empty($params) && ($query = http_build_query($params)) !== '')
        {
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * @param \yii\web\UrlManager   $manager
     * @param \yii\web\Request      $request
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo       = $request->getPathInfo();
        $params         = $request->getQueryParams();
        $firstPrefix    = substr($pathInfo, 0, strlen($this->urlPrefix));

        if ($firstPrefix == $this->urlPrefix)
        {
            $route = str_replace($this->urlPrefix, "", $pathInfo);
            return [$route, $params];
        }

        return false;
    }
}
