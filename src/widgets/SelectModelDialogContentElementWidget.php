<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 21.08.2017
 */
namespace skeeks\cms\backend\widgets;
use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\backend\widgets\assets\SelectModelDialogWidgetAsset;
use skeeks\cms\Exception;
use skeeks\cms\helpers\Image;
use skeeks\cms\models\CmsContent;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\Publication;
use skeeks\cms\modules\admin\Module;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Application;
use yii\widgets\InputWidget;
use Yii;

/**
 * Class SelectModelDialogContentElementWidget
 * @package skeeks\cms\backend\widgets
 */
class SelectModelDialogContentElementWidget extends SelectModelDialogWidget
{
    /**
     * @var null
     */
    public $content_id = null;

    public $modelClassName = 'skeeks\cms\models\CmsContentElement';

    public $dialogRoute = ['/cms/admin-cms-content-element'];

    public function init()
    {
        if ($this->content_id)
        {
            //throw new InvalidConfigException('Need content_id');
            $this->dialogRoute = ArrayHelper::merge($this->dialogRoute, ['content_id' => $this->content_id]);
        }

        if (!$this->initClientDataModelCallback)
        {
            $this->initClientDataModelCallback = function(CmsContentElement $cmsContentElement)
            {
                return ArrayHelper::merge($cmsContentElement->toArray(), [
                    'image' => $cmsContentElement->image ? $cmsContentElement->image->src : '',
                    'url' => $cmsContentElement->url,
                ]);
            };
        }

        if (!$this->previewValueClientCallback)
        {
            $imageSrc = Image::getCapSrc();
            $this->previewValueClientCallback = new \yii\web\JsExpression(<<<JS
            function(data)
            {
                var imagesrc = '{$imageSrc}';
                if (data.image)
                {
                    imagesrc = data.image;
                }
                
                return '<img src="' + imagesrc + '" style="max-width: 50px; max-height: 50px;" /> <a href="' + data.url + '" target="_blank" data-pjax="0">' + data.name + '</a>'
            }
JS
            );
        }


        parent::init();
    }
}