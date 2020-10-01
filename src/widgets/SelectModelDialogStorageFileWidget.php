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
use skeeks\cms\models\CmsStorageFile;
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
class SelectModelDialogStorageFileWidget extends SelectModelDialogWidget
{
    public $modelClassName = 'skeeks\cms\models\CmsStorageFile';

    public $dialogRoute = ['/cms/admin-storage-files'];


    public $visibleInput = false;

    public function init()
    {
        if (!$this->initClientDataModelCallback)
        {
            $this->initClientDataModelCallback = function(CmsStorageFile $cmsStorageFile)
            {
                return ArrayHelper::merge($cmsStorageFile->toArray(), [
                    //'image' => $cmsUser->image ? $cmsUser->image->src : '',
                ]);
            };
        }

        if (!$this->previewValueClientCallback)
        {
            $imageSrc = Image::getCapSrc();
            $this->previewValueClientCallback = new \yii\web\JsExpression(<<<JS
            function(data) {
                if (data.image_height) {
                    return '<a href="' + data.src + '" target="_blank" data-pjax="0"><img src="' + data.src + '" style="max-width: 50px; max-height: 30px;" /></a>';
                }
                
                return '';
            }
JS
            );
        }

        parent::init();
    }
}