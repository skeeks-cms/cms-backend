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

    public function init()
    {
        $this->dialogRoute = ['/cms/admin-user'];
        parent::init();
    }

    /**
     * @return string
     */
    public function getPreviewValue()
    {
        if (!parent::getPreviewValue())
        {
            if ($this->user)
            {
                return Html::a($this->user->displayName, '#');
            }

        }

        return '';
    }

    /**
     * @return null|CmsContentElement
     */
    public function getUser()
    {
        if ($this->hasModel() && $this->model->{$this->attribute})
        {
            return CmsUser::findOne($this->model->{$this->attribute});
        }

        if ($this->value)
        {
            return CmsUser::findOne($this->value);
        }

        return null;
    }

}