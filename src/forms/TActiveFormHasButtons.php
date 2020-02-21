<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\forms;

use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
trait TActiveFormHasButtons
{
    /**
     * @param Model $model
     * @param array $buttons
     * @return string
     */
    public function buttonsStandart(Model $model, $buttons = ['apply', 'save', 'close'])
    {
        $baseData = [];
        $baseData['indexUrl'] = ((\Yii::$app->controller && isset(\Yii::$app->controller->url)) ? \Yii::$app->controller->url : "");
        if (\Yii::$app->request->referrer) {
            $baseData['indexUrl'] = \Yii::$app->request->referrer;
        }
        $baseData['input-id'] = $this->id.'-submit-btn';
        $baseData['form-id'] = $this->id;

        $baseDataJson = Json::encode($baseData);

        $submit = "";
        if (in_array("save", $buttons)) {
            $submit .= Html::submitButton("<i class=\"fa fa-check\"></i> ".\Yii::t('skeeks/cms', 'Сохранить и закрыть'), [
                'title'   => 'Результат будет сохранен и окно редактирования будет закрыто',
                'class'   => 'btn btn-primary',
                'onclick' => "return sx.CmsActiveFormButtons.go('save');",
            ]);
        }

        if (in_array("apply", $buttons)) {
            $submit .= ' '.Html::submitButton("<i class=\"fa fa-check\"></i> ".\Yii::t('skeeks/cms',
                        'Save'), [
                    'title'   => 'Результат будет сохранен и вы сможете дальше редактировать данные.',
                    'class'   => 'btn btn-primary',
                    'onclick' => "return sx.CmsActiveFormButtons.go('apply');",
                ]);
        }

        if (in_array("close", $buttons)) {
            $submit .= ' '.Html::submitButton("<i class=\"fa fa-times\"></i> ".\Yii::t('skeeks/cms',
                        'Cancel'), [
                    'class'   => 'btn btn-secondary pull-right',
                    'onclick' => "return sx.CmsActiveFormButtons.go('close');",
                ]);
        }

        $submit .= Html::hiddenInput("submit-btn", 'apply', [
            'id' => $baseData['input-id'],
        ]);

        \Yii::$app->view->registerJs(<<<JS
    (function(sx, $, _)
    {
     
        sx.classes.CmsActiveFormButtons = sx.classes.Component.extend({
        
            go: function(value)
            {
                if (value == "close")
                {
                    if (sx.Window.openerWidget()) {
                        sx.Window.openerWidget().close();
                        return false;
                    } else
                    {
                        window.location = this.get('indexUrl');
                        return false;
                    }
                    
                    return false;
                } else
                {
                    $("#" + this.get('input-id')).val(value);
                }

                return true;
            },
            
            _onWindowReady: function()
            {
                var self = this;
                
                this._initStartPoint();
                self.updatePositions();
                
                
                
                $(window).on('resize', function(){
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                });
                
                $(window).scroll(function(){
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                });
                
                _.delay(function() {
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                }, 500);
                
                _.delay(function() {
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                }, 1000);
            },
            
            getJForm: function() {
                return $("#" + this.get("form-id"));
            },
            
            getJButtons: function() {
                return $(".sx-buttons-standart-wrapper", this.getJForm());
            },
            
            _initStartPoint: function() {
                this.startPosition = this.getJButtons().offset().top;
                this.startPositionLeft = this.getJButtons().offset().left;
                this.buttonsWidth = this.getJButtons().width();
                this.buttonsHeight = this.getJButtons().height();
                return this;
            },
            
            _enableFixed: function() {
                this.getJButtons()
                    .css("width", this.buttonsWidth)
                    .css("position", "fixed")
                    .css("bottom", "0")
                    .css("left", this.startPositionLeft)
                    .css("background-color", "white")
                    .addClass("sx-fixed-buttons")
                ;
                return this;
            },
            
            _disableFixed: function() {
                this.getJButtons()
                        .removeAttr("style")
                        .removeClass("sx-fixed-buttons")
                    ;
                return this;
            },
            
            updatePositions: function() {
                
                var pageYOffset = window.pageYOffset;
                var scrolled = $(this).scrollTop();
                var height = window.innerHeight;
                var heightAll = pageYOffset + height;
                
                if (heightAll  < this.startPosition) {
                    this._enableFixed();
                } else {
                    this._disableFixed();
                }
                /*console.log("pageYOffset: " + pageYOffset)
                console.log("offset top: " + this.startPosition)
                console.log("height: " + height)
                console.log("heightAll: " + heightAll)*/
            }
        });

        sx.CmsActiveFormButtons = new sx.classes.CmsActiveFormButtons({$baseDataJson});
    })(sx, sx.$, sx._);
JS
        );

        return "<div class='sx-buttons-standart-wrapper'>" . Html::tag('div',
            $submit,
            ['class' => 'form-group sx-buttons-standart']
        )  . "</div>";
    }


}