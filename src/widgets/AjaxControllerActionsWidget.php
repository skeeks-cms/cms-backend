<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\widgets\assets\AjaxControllerActionsWidgetAsset;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class AjaxControllerActionsWidget extends Widget
{
    public $defaultOptions = [
        "class" => "btn btn-xs",
    ];
    
    
    public $options = [];


    /**
     * @var string
     */
    public $tag = "div";

    /**
     * @var string 
     */
    public $content = '<i class="fa fa-caret-down"></i>';
    
    /**
     * @var string
     */
    public $controllerId;

    /**
     * @var
     */
    public $modelId;

    /**
     * @throws Exception
     */
    public function init()
    {

        parent::init();

        if (!$this->controllerId) {
            throw new Exception("Need controller id");
        }

        ob_start();
        ob_implicit_flush(false);
    }


    /**
     * @var bool
     */
    static protected $_registerAsset = false;

    /**
     *
     */
    static public function registerAssets()
    {
        if (self::$_registerAsset) {
            return;
        }

        self::$_registerAsset = true;
        AjaxControllerActionsWidgetAsset::register(\Yii::$app->view);
        $jsConfig = Json::encode([
            'loader' => AjaxControllerActionsWidgetAsset::getAssetUrl('img/loader/rolling-1s-24px.svg'),
        ]);

        \Yii::$app->view->registerJs(<<<JS
new sx.classes.AjaxControllerActionsWidget({$jsConfig});
JS
        );
        return;
    }
    /**
     * @return string
     */
    public function run()
    {
        self::registerAssets();

        $this->options = ArrayHelper::merge($this->defaultOptions, [
            'data' => [
                'url'           => Url::to(["/".$this->controllerId."/model-actions", 'pk' => $this->modelId]),
                'controller-id' => $this->controllerId,
                'model-id'      => $this->modelId,
            ],
        ], $this->options);

        $content = ob_get_clean();
        if (!$content) {
            $content = $this->content;
        }

        Html::addCssClass($this->options, 'sx-btn-ajax-actions');

        return Html::tag($this->tag, $content, (array)$this->options);

    }
}