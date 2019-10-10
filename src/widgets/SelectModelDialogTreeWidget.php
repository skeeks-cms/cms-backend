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
use skeeks\cms\models\CmsTree;
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
class SelectModelDialogTreeWidget extends SelectModelDialogWidget
{
    public $modelClassName = 'skeeks\cms\models\CmsTree';

    public $dialogRoute = ['/cms/admin-tree'];

    public function init()
    {
        if (!$this->initClientDataModelCallback)
        {
            $this->initClientDataModelCallback = function(CmsTree $cmsTree)
            {
                $result = ArrayHelper::merge($cmsTree->toArray(), [
                    'image' => $cmsTree->image ? $cmsTree->image->src : '',
                    'url' => $cmsTree->url,
                    'fullName' => $cmsTree->fullName,
                ]);
                ArrayHelper::remove($result, 'description_full');
                ArrayHelper::remove($result, 'description_short');
                return $result;
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
                
                return '<img src="' + imagesrc + '" style="max-width: 50px; max-height: 50px;" /> <a href="' + data.url + '" target="_blank" data-pjax="0">' + data.fullName + '</a>'
            }
JS
            );
        }


        parent::init();
    }
}