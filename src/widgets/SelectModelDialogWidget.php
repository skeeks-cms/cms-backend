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
 *
 *
 * <?= $form->field($model, 'name')->widget(
        \skeeks\cms\backend\widgets\SelectModelDialogWidget::class,
        [
            'dialogRoute' => ['/cms/admin-user'],
            'previewValueClientCallback' => new \yii\web\JsExpression(<<<JS
function(data)
{
console.log(data);
return '<a href="' + data.id + '" target="_blank" data-pjax="0">' + data.name + '</a>'
}
JS
            ),
            'previewValueCallback' => function(\skeeks\cms\backend\widgets\SelectModelDialogWidget $selectModelDialogWidget)
            {
                return $selectModelDialogWidget->model->name;
            }
        ]
    ); ?>
*
*
 * @property string $url
 * @property string|array $inputValue
 * @property string $callbackEventName
 * @property string $previewValue
 *
 * @package skeeks\widget\SelectModelDialog
 */
class SelectModelDialogWidget extends InputWidget
{
    public static $autoIdPrefix = 'SelectModelDialogWidget';
    /**
     * @var array
     */
    public $clientOptions = [];
    /**
     * @var array
     */
    public $dialogRoute = [];
    /**
     * @var boolean whether to show deselect button on single select
     */
    public $allowDeselect = true;

    /**
     * @var bool
     */
    public $closeDialogAfterSelect = true;

    /**
     * @var bool
     */
    public $visibleInput = true;

    /**
     * @var bool
     */
    public $multiple = false;

    /**
     * @var null|callable
     */
    public $previewValueCallback = null;

    /**
     * @var null|string
     */
    public $previewValueClientCallback = null;


    public $viewFile  = 'select-model-dialog';

    public function init()
    {
        /*if (!$this->hasModel())
        {
            throw new InvalidConfigException('Only model');
        }*/
        if (!$this->id)
        {
           $this->id = $this->id . "-" . \Yii::$app->security->generateRandomString(10);
        }

        $this->clientOptions['id'] = $this->id;
        parent::init();
    }
    /**
     * @return string
     */
    public function getUrl()
    {
        $additionalData = BackendUrlHelper::createByParams($this->dialogRoute)
                ->enableEmptyLayout()
                ->setCallbackEventName($this->callbackEventName)
                ->params
            ;
        return Url::to($additionalData);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        SelectModelDialogWidgetAsset::register($this->view);

        $input = '';
        if ($this->model)
        {
            if ($this->multiple)
            {
                $this->options['multiple'] = true;
                $input = \yii\helpers\Html::activeListBox($this->model, $this->attribute, $this->inputValue, $this->options);
            } else
            {
                Html::addCssClass($this->options, 'form-control');
                $input = \yii\helpers\Html::activeTextInput($this->model, $this->attribute, $this->options);
            }

        } else
        {
            if ($this->multiple)
            {
                $this->options['multiple'] = true;
                $input = \yii\helpers\Html::listBox($this->id, $this->attribute, $this->inputValue, $this->options);
            } else
            {
                Html::addCssClass($this->options, 'form-control');
                $input = \yii\helpers\Html::textInput($this->id, $this->attribute, $this->options);
            }

        }

        $this->clientOptions['multiple'] = $this->multiple;
        $this->clientOptions['callbackEventName'] = $this->callbackEventName;
        $this->clientOptions['url'] = $this->url;
        $this->clientOptions['closeDialogAfterSelect'] = $this->closeDialogAfterSelect;

        if ($this->previewValueClientCallback)
        {
            $this->clientOptions['previewValueClientCallback'] = $this->previewValueClientCallback;
        }

        return $this->render($this->viewFile, [
            'input' => $input
        ]);
    }


    /**
     * @return string
     */
    public function getCallbackEventName()
    {
        return $this->id . '-select-dialog';
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        if ($this->hasModel())
        {
            return (bool) $this->model->{$this->attribute};
        } else
        {
            return (bool) $this->value;
        }
    }

    /**
     * @return mixed|string
     */
    public function getInputValue()
    {
        if ($this->hasModel())
        {
            return $this->model->{$this->attribute};
        } else
        {
            return $this->value;
        }
    }

    /**
     * @return string
     */
    public function getPreviewValue()
    {
        if ($this->previewValueCallback && is_callable($this->previewValueCallback))
        {
            $previewModelCallback = $this->previewValueCallback;
            return $previewModelCallback($this);
        }

        return '';
    }
}