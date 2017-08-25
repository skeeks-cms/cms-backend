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
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsUser;
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
 * @property CmsUser $user;
 *
 * Class SelectModelDialogUserWidget
 * @package skeeks\cms\backend\widgets
 */
class SelectModelDialogUserWidget extends SelectModelDialogWidget
{

    public $modelClassName = 'skeeks\cms\models\CmsUser';

    public $dialogRoute = ['/cms/admin-user'];

    public function init()
    {
        if (!$this->initClientDataModelCallback)
        {
            $this->initClientDataModelCallback = function(CmsUser $cmsUser)
            {
                return ArrayHelper::merge($cmsUser->toArray(), [
                    'image' => $cmsUser->image ? $cmsUser->image->src : '',
                    //'url' => $cmsUser->url,
                    'displayName' => $cmsUser->displayName,
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
                
                return '<img src="' + imagesrc + '" style="max-width: 50px; max-height: 50px;" /> <a href="#" target="_blank" data-pjax="0">' + data.displayName + '</a>'
            }
JS
            );
        }

        parent::init();
    }
}