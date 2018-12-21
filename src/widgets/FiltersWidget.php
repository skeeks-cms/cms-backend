<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\widgets\filters\ActiveField;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\queryfilters\QueryFiltersWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
//class FiltersWidget extends \skeeks\cms\widgets\FiltersWidget {
class FiltersWidget extends QueryFiltersWidget
{

    public $viewFile = 'filters';
    public $isOpened = false;

    public $defaultActiveForm = [
        'class'       => ActiveForm::class,
        'fieldClass'  => ActiveField::class,
        'layout'      => 'horizontal',
        'options'     => [
            'class'     => 'sx-backend-filters-form ',
            'data-pjax' => 1,
        ],
        'method'      => 'get',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n<div class='sx-filter-wrapper'>{input}</div>\n{hint}\n{error}\n{endWrapper}{controlls}",
        ],
    ];

    public function init()
    {
        /*$defaultOptions = [
            'class' => ActiveForm::class,
            'fieldClass' => ActiveField::class,
            'layout' => 'horizontal',
            'options' => [
                'class' => 'sx-backend-filters-form',
                'data-pjax' => 1
            ],
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n<div class='sx-filter-wrapper'>{input}</div>\n{hint}\n{error}\n{endWrapper}{controlls}",
            ]
        ];*/

        $this->activeForm = ArrayHelper::merge($this->defaultActiveForm, $this->activeForm);

        parent::init();

        Html::addCssClass($this->wrapperOptions, 'sx-backend-filters-wrapper');
    }

    public function run()
    {
        if (\Yii::$app->request->isPost && \Yii::$app->request->isAjax) {
            if (\Yii::$app->request->post('act')) {
                ob_get_clean();
                $rr = new RequestResponse();
                if (\Yii::$app->request->post('act') == 'remove-item') {

                    $visibles = $this->visibleFilters;
                    ArrayHelper::removeValue($visibles, \Yii::$app->request->post('attribute'));

                    $this->configModel->visibleFilters = $visibles;

                    if (!$this->configBehavior->saveConfig()) {
                        $rr->success = false;
                        $rr->message = "Настройки не сохранены";
                    } else {
                        $rr->success = true;
                        //$rr->message = 'Настройки сохранены';
                    }

                } elseif (\Yii::$app->request->post('act') == 'save-values') {
                    try {
                        $this->filtersModel->load(\Yii::$app->request->post());
                        $this->configModel->filterValues = $this->filtersModel->toArray();

                        if (!$this->configBehavior->saveConfig()) {
                            $rr->success = false;
                            $rr->message = "Настройки не сохранены";
                        } else {
                            $rr->success = true;
                            //$rr->message = 'Настройки сохранены';
                        }
                    } catch (\Exception $e) {

                    }
                } elseif (\Yii::$app->request->post('act') == 'sort') {
                    try {
                        $this->configModel->visibleFilters = \Yii::$app->request->post('sort');

                        if (!$this->configBehavior->saveConfig()) {
                            $rr->success = false;
                            $rr->message = "Настройки не сохранены";
                        } else {
                            $rr->success = true;
                            //$rr->message = 'Настройки сохранены';
                        }

                    } catch (\Exception $e) {

                    }
                }

                \Yii::$app->response->data = $rr;
                \Yii::$app->end();
            }

        }

        $jsOptions = Json::encode([
            'id' => $this->id,
        ]);

        \yii\jui\Sortable::widget();

        $this->view->registerJs(<<<JS
(function(sx, $, _)
{   
    sx.createNamespace('classes.widgets', sx); 
    
    sx.classes.widgets.BackendFiltersWidget = sx.classes.Component.extend({
    
        _init: function()
        {},
        
        _onDomReady: function()
        {
            var self = this;
            
            this.jWrapper = $('#' + this.get('id'));
            this.jForm = $("form", this.jWrapper);
            
            this.Blocker = new sx.classes.Blocker('#' + this.get('id'));
            
            $(".sx-remove", this.jWrapper).on('click', function() {
                var jRemove = $(this);
                
                var jFG = jRemove.closest('.form-group');
                self.removeAttribute(jFG.data('attribute'));
                
                jFG.slideUp('normal', function() {
                    jFG.remove();
                });
                
                return false;
            });
            
            $(".sx-save-values", this.jWrapper).on('click', function() {
                self.saveValues();
                return false;
            });
            
            $(".sx-edit-trigger", this.jWrapper).on('click', function() {
                $(".sx-edit").click();
                return false;
            });
            
            this._initSortable();
        },
        
        _initSortable: function() {
            var self = this;
            //$('.form-group', this.jForm).sortable({
            this.jForm.sortable({
                cursor: "move",
                handle: ".sx-move",
                forceHelperSize: true,
                forcePlaceholderSize: true,
                opacity: 0.5,
                placeholder: "ui-state-highlight",
                
                out: function( event, ui )
                {
                    var newSort = [];
                    
                    if ($('[name=act]', self.jForm).length) {
                        $('[name=act]', self.jForm).val('sort');
                    } else {
                        self.jForm.append(
                            $("<input>", {
                                'name' : 'act',
                                'value' : 'sort',
                                'type' : 'hidden'
                            })
                        );
                    }
                    
                    var jSort = null;
                    if ($('.sx-sort-values', self.jForm).length) {
                        jSort = $('.sx-sort-values', self.jForm);
                        jSort.empty();
                    } else {
                        jSort = $("<select>", {
                            'name' : 'sort[]',
                            'style' : 'display: none;',
                            'multiple' : 'multiple',
                            'class' : 'sx-sort-values',
                        });
                        self.jForm.append(
                            jSort
                        );
                    }
                    
                    self.jForm.children(".form-group").each(function(i, element)
                    {
                        newSort.push($(this).data("attribute"));
                        
                        jSort.append(
                            $("<option>", {
                                'value' : $(this).data("attribute"),
                                'selected' : 'selected'
                            }).text($(this).data("attribute"))
                        );
                    });

                    var data = self.jForm.serializeArray();
        
                    var ajaxQuery = sx.ajax.preparePostQuery(self.jForm.attr('action'), data);
                    
                    var handler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery, {
                        'blocker' : self.Blocker,
                        'enableBlocker' : true,
                    });
                    new sx.classes.AjaxHandlerNoLoader(ajaxQuery);
                    
                    ajaxQuery.execute();
                }
            });
        },
        
        removeAttribute: function(attribute)
        {
            var self = this;
            
            var data = this.jForm.serializeArray();

            if ($('[name=act]', self.jForm).length) {
                $('[name=act]', self.jForm).val('remove-item');
            } else {
                self.jForm.append(
                    $("<input>", {
                        'name' : 'act',
                        'value' : 'remove-item',
                        'type' : 'hidden'
                    })
                );
            }
        
            self.jForm.append(
                $("<input>", {
                    'name' : 'attribute',
                    'value' : attribute,
                    'type' : 'hidden'
                })
            );
            
            var data = this.jForm.serializeArray();
        
            var ajaxQuery = sx.ajax.preparePostQuery(this.jForm.attr('action'), data);
            
            var handler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery, {
                'blocker' : self.Blocker,
                'enableBlocker' : true,
            });
            new sx.classes.AjaxHandlerNoLoader(ajaxQuery);
            
            ajaxQuery.execute();
        },
        
        saveSort: function(attributes)
        {
            var data = this.jForm.serializeArray();

            if ($('[name=act]', self.jForm).length) {
                $('[name=act]', self.jForm).val('remove-item');
            } else {
                self.jForm.append(
                    $("<input>", {
                        'name' : 'act',
                        'value' : 'remove-item',
                        'type' : 'hidden'
                    })
                );
            }
        
        
        
            self.jForm.append(
                $("<input>", {
                    'name' : 'attribute',
                    'value' : attribute,
                    'type' : 'hidden'
                })
            );
            
            var data = this.jForm.serializeArray();
        
            var ajaxQuery = sx.ajax.preparePostQuery(this.jForm.attr('action'), data);
            
            var handler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery);
            
            ajaxQuery.execute();
        },
        
        saveValues: function()
        {
            var self = this;
            
            if ($('[name=act]', self.jForm).length) {
                $('[name=act]', self.jForm).val('save-values');
            } else {
                self.jForm.append(
                    $("<input>", {
                        'name' : 'act',
                        'value' : 'save-values',
                        'type' : 'hidden'
                    })
                );
            }
            
            
            var data = this.jForm.serializeArray();

            var action = this.jForm.attr('action');
            if (this.jForm.data('real-action')) {
                action = this.jForm.data('real-action');
            }
            
            var ajaxQuery = sx.ajax.preparePostQuery(action, data);
            
            var handler = new sx.classes.AjaxHandlerStandartRespose(ajaxQuery, {
                'blocker' : self.Blocker,
                'enableBlocker' : true,
            });
            new sx.classes.AjaxHandlerNoLoader(ajaxQuery);
            
            ajaxQuery.execute();
        }
    });
    
    new sx.classes.widgets.BackendFiltersWidget({$jsOptions});
})(sx, sx.$, sx._);
JS
        );

        return parent::run();
    }
}