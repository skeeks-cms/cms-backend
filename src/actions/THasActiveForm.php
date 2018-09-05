<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\actions;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/**
 * @property null|ActiveForm $activeForm read-only;
 * @property string $activeFormClassName;
 *
 * Interface IHasActiveForm
 *
 * @package skeeks\cms\backend\actions
 */
trait THasActiveForm
{
    /**
     * @var string
     */
    public $activeFormClassName = 'skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab';

    /**
     * @var null|ActiveForm|ActiveFormUseTab
     */
    protected $_activeForm = null;

    /**
     * @var string
     */
    public $reloadFormParam = 'sx-reload-form';

    /**
     * @var string
     */
    public $reloadFieldParam = 'data-form-reload';

    /**
     * @var array
     */
    public $buttons = ['apply', 'save', 'close'];

    /**
     * @param array $config
     *
     * @return ActiveForm|ActiveFormUseTab
     */
    public function beginActiveForm(array $config = [])
    {
        $className = $this->activeFormClassName;
        $this->_activeForm = $className::begin($config);
        return $this->_activeForm;
    }


    public function beginDynamicActiveForm(array $config = [])
    {
        $form = $this->beginActiveForm([
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
        ]);

        $jsOptions = Json::encode([
            'id' => $form->id,
            'reload_param' => $this->reloadFormParam,
            'field_reload_param' => $this->reloadFieldParam
        ]);
        \Yii::$app->view->registerJs(<<<JS

(function(sx, $, _)
{
    sx.classes.DynamicForm = sx.classes.Component.extend({

        _onDomReady: function()
        {
            var self = this;

            $("[" + this.get('field_reload_param') + "]").on('change', function()
            {
                self.update();
            });
        },

        update: function()
        {
            var self = this;
            _.delay(function()
            {
                var jForm = $("#" + self.get('id'));
                jForm.append($('<input>', {'type': 'hidden', 'name' : self.get('reload_param'), 'value': 'true'}));
                jForm.submit();
            }, 200);
        }
    });

    sx.DynamicForm = new sx.classes.DynamicForm({$jsOptions});
})(sx, sx.$, sx._);


JS
);
        return $form;
    }

    /**
     * @param array $config
     *
     * @return mixed
     */
    public function endActiveForm(array $config = [])
    {
        $className = $this->activeFormClassName;
        return $className::end();
    }

    /**
     * @return null|ActiveFormUseTab|ActiveForm
     */
    public function getActiveForm()
    {
        return $this->_activeForm;
    }
}