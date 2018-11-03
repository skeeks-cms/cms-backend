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
use skeeks\cms\base\ActiveRecord;
use skeeks\cms\Exception;
use skeeks\cms\models\Publication;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 *
 *
 * <?= $form->field($model, 'name')->widget(
 * \skeeks\cms\backend\widgets\SelectModelDialogWidget::class,
 * [
 * 'dialogRoute' => ['/cms/admin-user'],
 * 'previewValueClientCallback' => new \yii\web\JsExpression(<<<JS
 * function(data)
 * {
 * console.log(data);
 * return '<a href="' + data.id + '" target="_blank" data-pjax="0">' + data.name + '</a>'
 * }
 * JS
 * ),
 * 'initClientDataModelCallback' => function($model)
 * {
 * return $model->toArray();
 * }
 * ]
 * ); ?>
 *
 *
 * @property string       $url
 * @property string|array $inputValue
 * @property string       $callbackEventName
 *
 * @property array        $initClientData
 *
 * @property string       $selectBtn
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
    public $wrapperOptions = [
        "class" => "row"
    ];
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
    public $previewValueClientCallback = null;

    /**
     * @var null|callable
     */
    public $initClientDataModelCallback = null;

    /**
     * @var null
     */
    public $modelClassName = null;


    public $viewFile = '@skeeks/cms/backend/widgets/views/select-model-dialog';

    protected $_selectBtn = [
        'tag'     => 'a',
        'content' => '<i class="fa fa-list" aria-hidden="true"></i>',
        'options' => [
            'class' => 'btn btn-default sx-btn-create btn-xs',
            'title' => 'Выбрать значение',
        ],
    ];

    public function init()
    {
        if (!$this->modelClassName) {
            throw new InvalidConfigException('Model class name is required');
        }

        if (!$this->id) {
            $this->id = $this->id."-".\Yii::$app->security->generateRandomString(10);
        }

        if (!isset($this->wrapperOptions['id'])) {
            $this->wrapperOptions['id'] = $this->id . "-wrapper";
        }
        $this->clientOptions['id'] = $this->wrapperOptions['id'];

        parent::init();
    }
    /**
     * @return array
     */
    public function getSelectBtn()
    {
        return $this->_selectBtn;
    }
    /**
     * @param array $selectBtn
     * @return $this
     */
    public function setSelectBtn($selectBtn = [])
    {
        $this->_selectBtn = ArrayHelper::merge($this->_selectBtn, $selectBtn);

        Html::addCssClass($this->_selectBtn['options'], 'sx-btn-create');
        return $this;

    }
    /**
     * @return string
     */
    public function getUrl()
    {
        $additionalData = BackendUrlHelper::createByParams($this->dialogRoute)
            ->enableEmptyLayout()
            ->setCallbackEventName($this->callbackEventName)
            ->params;
        return Url::to($additionalData);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        SelectModelDialogWidgetAsset::register($this->view);

        $input = '';
        if ($this->model) {
            if ($this->multiple) {
                $this->options['multiple'] = true;

                $items = [];
                if ($this->inputValue && is_array($this->inputValue)) {
                    foreach (array_values($this->inputValue) as $id) {
                        if (is_object($id)) {
                            //TODO: не всегда id
                            $items[$id->id] = $id->id;
                        } else {
                            $items[$id] = $id;
                        }
                    }
                }

                $input = \yii\helpers\Html::activeListBox($this->model, $this->attribute, $items, $this->options);
            } else {
                Html::addCssClass($this->options, 'form-control');
                $input = \yii\helpers\Html::activeTextInput($this->model, $this->attribute, $this->options);
            }

        } else {
            if ($this->multiple) {
                $this->options['multiple'] = true;
                $items = [];
                $value = [];
                if ($this->inputValue && is_array($this->inputValue)) {
                    $value = $this->inputValue;
                    foreach (array_values($this->inputValue) as $id) {
                        if (is_object($id)) {
                            //TODO: не всегда id
                            $items[$id->id] = $id->id;
                        } else {
                            $items[$id] = $id;
                        }
                    }
                }
                $input = \yii\helpers\Html::listBox($this->name, $value, $items, $this->options);
            } else {
                Html::addCssClass($this->options, 'form-control');
                $input = \yii\helpers\Html::textInput($this->name, $this->inputValue, $this->options);
            }

        }

        $this->clientOptions['multiple'] = $this->multiple;
        $this->clientOptions['callbackEventName'] = $this->callbackEventName;
        $this->clientOptions['url'] = $this->url;
        $this->clientOptions['closeDialogAfterSelect'] = $this->closeDialogAfterSelect;

        if ($this->previewValueClientCallback) {
            $this->clientOptions['previewValueClientCallback'] = $this->previewValueClientCallback;
        } else {
            $this->clientOptions['previewValueClientCallback'] = new \yii\web\JsExpression(<<<JS
                function(data)
                {
                    return '<a href="#" target="_blank" data-pjax="0">' + data.asText + '</a>'
                }
JS
            );
                }
        if ($initClientData = $this->initClientData) {
            $this->clientOptions['initClientData'] = $initClientData;
        }

        return $this->render($this->viewFile, [
            'input' => $input,
        ]);
    }


    /**
     * @return string
     */
    public function getCallbackEventName()
    {
        return $this->id.'-select-dialog';
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        if ($this->hasModel()) {
            $value = isset($this->value) ? $this->value : Html::getAttributeValue($this->model, $this->attribute);
            return (bool)$value;
        } else {
            return (bool)$this->value;
        }
    }

    /**
     * @return mixed|string
     */
    public function getInputValue()
    {
        $value = null;

        if ($this->hasModel()) {
            $value = isset($this->value) ? $this->value : Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }

        if ($this->multiple) {
            if (!is_array($value)) {
                $newVal = [];
                $newVal[] = $value;
                $value = $newVal;
            }
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getInitClientData()
    {
        $result = [];
        $modelClassName = $this->modelClassName;

        if ($this->inputValue) {
            if (is_array($this->inputValue)) {
                $result = [];
                $models = $modelClassName::find()->andWhere(['id' => $this->inputValue])->all();
                foreach ($models as $model) {
                    if ($initClientDataModelCallback = $this->initClientDataModelCallback) {
                        $result[] = (array)$initClientDataModelCallback($model);
                    } else {
                        if ($model instanceof ActiveRecord) {
                            $result[] = ArrayHelper::merge($model->toArray(), [
                                'asText' => $model->asText
                            ]);
                        } else {
                            $result[] = $model->toArray();
                        }
                    }

                }
            } else {
                $model = $modelClassName::find()->andWhere(['id' => $this->inputValue])->one();

                if ($initClientDataModelCallback = $this->initClientDataModelCallback) {
                    $result = (array)$initClientDataModelCallback($model);
                } else {
                    if ($model instanceof ActiveRecord) {
                        $result = ArrayHelper::merge($model->toArray(), [
                            'asText' => $model->asText
                        ]);
                    } else {
                        $result = $model->toArray();
                    }

                }
            }
        }

        return $result;
    }
}