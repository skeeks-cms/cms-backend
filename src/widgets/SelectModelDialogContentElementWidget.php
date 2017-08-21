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
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\Publication;
use skeeks\cms\modules\admin\Module;
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
    public static $autoIdPrefix = 'SelectModelDialogContentElementWidget';
    /**
     * @var null
     */
    public $content_id = null;

    public function init()
    {
        $this->dialogRoute = ['/cms/admin-cms-content-element', 'content_id' => $this->content_id];


        parent::init();
    }

    /**
     * @return string
     */
    public function getPreviewValue()
    {
        if (!parent::getPreviewValue())
        {
            return Html::a($this->model->name, '#');
        }

        return '';
    }

}