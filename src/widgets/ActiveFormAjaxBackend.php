<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\forms\ActiveFormHasCustomSelectTrait;
use skeeks\cms\backend\forms\TActiveFormHasButtons;
use skeeks\cms\backend\forms\TActiveFormHasCustomSelect;
use skeeks\cms\backend\widgets\assets\BackendFormAsset;
use skeeks\cms\forms\ActiveFormHasFieldSetsTrait;
use skeeks\cms\forms\ActiveFormHasPjaxTrait;
use skeeks\cms\forms\IActiveFormHasFieldSets;
use skeeks\cms\forms\TActiveFormDynamicReload;
use skeeks\cms\forms\TActiveFormHasFieldSets;
use skeeks\cms\traits\ActiveFormAjaxSubmitTrait;
use yii\helpers\Html;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ActiveFormAjaxBackend extends ActiveForm implements IActiveFormHasFieldSets
{
    use TActiveFormHasFieldSets;
    use TActiveFormHasButtons;
    use TActiveFormHasCustomSelect;

    use TActiveFormDynamicReload;

    use ActiveFormAjaxSubmitTrait;


    public $validateOnChange = true;
    public $validateOnBlur = true;


    public static $autoIdPrefix = "bf";

    /**
     * @var string
     */
    //public $fieldClass = ActiveFieldBackend::class;
    public $fieldClass = ActiveField::class;

    /**
     * @var bool Подключить стандартные js и css?
     */
    public $registerStandartAsset = true;


    public $clientSuccess = null;


    public function init()
    {
        Html::addCssClass($this->options, "sx-backend-form");
        Html::addCssClass($this->options, "sx-backend-form-inline");

        if ($this->registerStandartAsset) {
            BackendFormAsset::register($this->view);
        }

        $this->_initDynamicReload();

        if (!$this->clientCallback) {
            $successCallback = 'null';
            if ($this->clientSuccess) {
                $successCallback = $this->clientSuccess;
            }
            $this->clientCallback = new \yii\web\JsExpression(<<<JS
                function (ActiveFormAjaxSubmit) {
                    
                    ActiveFormAjaxSubmit.on('success', function(e, response) {
                        
                        var clientSuccess = {$successCallback};
                        if (clientSuccess) {
                            if (clientSuccess(ActiveFormAjaxSubmit) === false) {
                                return false;
                            }
                        }
                        
                        $('.sx-buttons-standart .sx-success-meessage', ActiveFormAjaxSubmit.jForm).empty().append(response.message);
                        ActiveFormAjaxSubmit.jForm.removeClass("sx-form-data-changed");

                        if (response.data.type == 'create') {
                            setTimeout(function() {
                                
                                 sx.Window.openerWidgetTriggerEvent('model-create', {
                                    'submitBtn' : 'save'
                                });
                            }, 1000);
                            
                        } else if (response.data.type == 'update') {
                            var submitBtn = response.data.submitBtn || 'apply';
                            
                            if (submitBtn == 'save') {
                                setTimeout(function() {
                                     sx.Window.openerWidgetTriggerEvent('model-update', {
                                        'submitBtn' : submitBtn
                                    });
                                }, 1000);
                            } else {
                                setTimeout(function() {
                                     sx.Window.openerWidgetTriggerEvent('model-update', {
                                        'submitBtn' : submitBtn
                                    });
                                }, 0);
                            }
                            
                            
                            
                        }
                        
                    });
                    
                    ActiveFormAjaxSubmit.on('error', function(e, response) {
                        $('.sx-buttons-standart .sx-success-meessage', ActiveFormAjaxSubmit.jForm).empty().append('<span style="color: red;">Есть ошибки!</span>');
                        
                    });
                }
JS
            );
        }


        parent::init();

    }


}