<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\widgets\assets\FormButtonsBackendWidgetAsset;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class FormButtonsBackendWidget extends Widget
{
    /**
     * @var null|Model
     */
    public $model = null;

    /**
     * @var null|ActiveForm
     */
    public $activeForm = null;

    /**
     * @var string
     */
    public $template = "<span class='sx-save-btns-group'>{saveAndClose} {save}</span><span class='sx-prev-next-btns-group sx-btns-group'>{prevNext}</span><span class='sx-other-btns-group sx-btns-group'>{other}</span><span class='sx-close-btns-group sx-btns-group pull-right'>{close}</span>";

    /**
     * @var string Дополнительные кнопки или контент
     */
    public $other = '';
    /**
     * @var null
     */
    public $content = null;

    /**
     * @var bool
     */
    public $isSaveAndClose = true;

    /**
     * @var bool
     */
    public $isSave = true;

    /**
     * Использовать кнопки вперед назад
     * @var bool
     */
    public $isPrevNext = true;

    /**
     * Включить перемещение кнопками клавиатуры
     * @var bool
     */
    public $isPrevNextKeyboard = true;


    /**
     * @var bool
     */
    public $isClose = true;


    /**
     * Разрешить возможность фиксирования кнопок внизу экрана
     * @var bool
     */
    public $isFixed = true;

    /**
     * @var array
     */
    public $parts = [];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->model || !$this->activeForm) {
            throw new InvalidConfigException("!!!");
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        $content = $this->content;
        if ($content === null) {
            if (!isset($this->parts['{saveAndClose}'])) {
                $this->_renderSaveAndClose();
            }
            if (!isset($this->parts['{save}'])) {
                $this->_renderSave();
            }
            if (!isset($this->parts['{prevNext}'])) {
                $this->_renderPrevNext();
            }
            if (!isset($this->parts['{close}'])) {
                $this->_renderClose();
            }
            if (!isset($this->parts['{other}'])) {
                $this->_renderOther();
            }

            $content = strtr($this->template, $this->parts);
        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }


        $baseData = [];

        //todo:что то старое
        $baseData['indexUrl'] = ((\Yii::$app->controller && isset(\Yii::$app->controller->url)) ? \Yii::$app->controller->url : "");
        if (\Yii::$app->request->referrer) {
            $baseData['indexUrl'] = \Yii::$app->request->referrer;
        }
        $baseData['input-id'] = $this->activeForm->id.'-submit-btn';
        $baseData['form-id'] = $this->activeForm->id;
        $baseData['isfixed'] = $this->isFixed;
        $baseData['isprevnext'] = $this->isPrevNext;
        $baseData['isprevnextkeyboard'] = $this->isPrevNextKeyboard;
        //todo: хардкод
        $baseData['model-id'] = isset($this->model->id) ? $this->model->id : null;

        $baseDataJson = Json::encode($baseData);

        FormButtonsBackendWidgetAsset::register($this->view);

        $this->view->registerJs(<<<JS
new sx.classes.form.FormButtonsBackend({$baseDataJson});
JS
        );

        return "<div class='sx-buttons-standart-wrapper'>".Html::tag('div',
                $content.
                Html::hiddenInput("submit-btn", 'apply', [
                    'id' => $baseData['input-id'],
                ]),
                ['class' => 'form-group sx-buttons-standart']
            )."</div>";
    }

    /**
     * @return $this
     */
    protected function _renderSaveAndClose()
    {
        $this->parts['{saveAndClose}'] = "";

        if ($this->isSaveAndClose) {
            $this->parts['{saveAndClose}'] = Html::submitButton("<i class=\"fa fa-check\"></i> ".\Yii::t('skeeks/backend', 'Save and close'), [
                'title' => \Yii::t("skeeks/backend", "The result will be saved and the editing window will be closed")." (ctrl+↵)",
                'class' => 'btn btn-primary sx-btn-save-and-close',
            ]);
        }

        return $this;
    }
    /**
     * @return $this
     */
    protected function _renderOther()
    {
        $this->parts['{other}'] = $this->other;

        return $this;
    }

    /**
     * @return $this
     */
    protected function _renderSave()
    {
        $this->parts['{save}'] = "";

        if ($this->isSave) {

            $this->parts['{save}'] = Html::submitButton("<i class=\"fa fa-check\"></i> ".\Yii::t('skeeks/backend', 'Save'), [
                'title' => \Yii::t('skeeks/backend', 'The result will be saved and you can further edit the data')." (ctrl+s)",
                'class' => 'btn btn-primary sx-btn-save',
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _renderClose()
    {
        $this->parts['{close}'] = "";

        if ($this->isClose) {

            $this->parts['{close}'] = Html::submitButton("<i class=\"fa fa-times\"></i> ".\Yii::t('skeeks/cms', 'Cancel'), [
                'title' => \Yii::t('skeeks/backend', 'The result will not be saved and the page will be closed')." (ctrl+z)",
                'class' => 'btn btn-secondary sx-btn-close',
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _renderPrevNext()
    {
        $this->parts['{prevNext}'] = "";

        if ($this->isPrevNext) {

            $submit = '<span class="sx-next-prev-btns" style="display: none;">'.Html::button("<i class=\"fas fa-arrow-left\"></i> ", [
                    'class' => 'btn u-btn-blue sx-btn-prev',
                    'title' => \Yii::t('skeeks/backend', 'Go to previous post')." (ctrl+←)",
                    'style' => 'display: none;',
                ]);

            $submit .= ' '.Html::button("<i class=\"fas fa-arrow-right\"></i> ", [
                    'class' => 'btn u-btn-blue sx-btn-next',
                    'title' => \Yii::t('skeeks/backend', 'Go to next post')." (ctrl+→)",
                    'style' => 'display: none;',
                ])."</span>";

            $this->parts['{prevNext}'] = $submit;
        }

        return $this;
    }
}