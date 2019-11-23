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
use yii\base\InvalidConfigException;
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
    public $options = [
        "class" => "dropdown-menu"
    ];

    /**
     * @var string
     */
    public $btn = [];
    /**
     * @var string
     */
    public $defaultBtn = [
        'tag' => 'a',
        'class' => 'btn btn-xs btn-default',
        'content' => '<i class="fa fa-caret-down"></i>'
    ];

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
    public function init() {

        parent::init();
        
        if (!$this->controllerId) {
            throw new Exception("Need controller id");
        }
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
            'loader' => AjaxControllerActionsWidgetAsset::getAssetUrl('img/loader/rolling-1s-24px.svg')
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

        $this->btn = ArrayHelper::merge($this->defaultBtn, [
            'data' => [
                'url' => Url::to(["/" . $this->controllerId . "/model-actions", 'pk' => $this->modelId]),
                'controller-id' => $this->controllerId,
                'model-id' => $this->modelId
            ]
        ], $this->btn);


        
        $tag = ArrayHelper::getValue($this->btn, 'tag');
        $content = ArrayHelper::getValue($this->btn, 'content');
        
        ArrayHelper::remove($this->btn, 'tag');
        ArrayHelper::remove($this->btn, 'content');
        
        Html::addCssClass($this->btn, 'sx-btn-ajax-actions');

        return Html::tag($tag, $content, (array) $this->btn);

    }
}