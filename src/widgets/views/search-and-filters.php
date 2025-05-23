<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 18.03.2018
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\backend\widgets\FiltersWidget */
$widget = $this->context;
$fields = $widget->filtersModel->builderFields();

$js = \yii\helpers\Json::encode([
    'id'       => $widget->id,
    'isOpened' => $widget->isOpened,
]);

$this->registerCss(<<<CSS
/*.sx-backend-filters-wrapper {
    background: var(--bg-block-color);
}*/

CSS
);

$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.BackendFilters = sx.classes.Component.extend({
    
        _init: function()
        {
            this.Cookie = new sx.classes.Cookie(this.get('id'));
        },
        
        _onDomReady: function()
        {
            var self = this;
            self.jWrapper = $("#" + this.get('id'));
            self.isOpened = this.get("isOpened");
            
            var jApplyBtn = $('.sx-filters-apply-btn', self.jWrapper);
            var jSaveBtn = $('.sx-save-values', self.jWrapper);
            var jAppliedContainer = $('.sx-applied-filters', self.jWrapper);
            var jFiltersBlock = $('.sx-filters-block', self.jWrapper);
            var jSearchInput = $('.sx-search-field input', self.jWrapper);
            
            var jForm = $("form", self.jWrapper);
            jForm.on("beforeSubmit", function(e, data) {
                
                ///var data = data || {};
                
                /*if (data.not_check) {
                    return true;
                }*/
                
                self.updateFastFilters();
                
                $(".form-group", jFiltersBlock).each(function() {
                   if ($(this).hasClass("sx-applied")) {
                   }  else {
                       $(this).remove();
                   }
                });
                
                if (jSearchInput.val()) {
                    
                } else {
                    jSearchInput.attr("disabled", "disabled");
                }
                
                return true;
                
                /*setTimeout(function() {
                    jForm.trigger("submit", {
                        'not_check': true
                    });
                }, 1000);
                
                
                return false;*/
            });
            
            $('.sx-filters-toggle', self.jWrapper).on("click", function() {
                
                if (self.isOpened === true) {
                    self.hide();
                    self.isOpened = false;
                    self.Cookie.set('opened', 'n');
                } else {
                    self.show();
                    self.isOpened = true;
                    self.Cookie.set('opened', 'y');
                }
                
                return false;
            });
            
            $('.sx-filters-settings-btn', self.jWrapper).on("click", function() {
                $(".sx-edit", self.jWrapper).click();
                return false;
            });
            
            
            
            var isNeedOpenFilter = false;
            
            if ($(".form-group", jFiltersBlock).length) {
                isNeedOpenFilter = true;
                
                //Сброс фильтров
                $(".form-group", jFiltersBlock).on("reset", function() {
                    var jGroup = $(this);
                    
                    //Установка мода по умолчанию
                    $("input", jGroup).val("");
                    $("select", jGroup).val("");
                    
                    var jModeSelect = $(".sx-filter-mode-wrapper select", jGroup);
                    if (jModeSelect.length) {
                        var jSelectModeWrapper = jModeSelect.closest('.sx-filter-mode-wrapper');
                        if (jSelectModeWrapper.data("default-mode") != jModeSelect.val()) {
                            jModeSelect.val(jSelectModeWrapper.data("default-mode")).change();
                        }
                    }
                });
                
                //Быстрые фильтры
                self.updateFastFilters();
                
            }
            
            $(document).mouseup(function (e) {
                
                var isClose = true;
                    if ($(e.target).closest(".sx-filters-block").length != 0){ 
                     isClose = false;
                  }
                    if ($(e.target).closest(".select2-container").length != 0){ 
                     isClose = false;
                  }
                    if ($(e.target).closest(".daterangepicker").length != 0){ 
                     isClose = false;
                  }
                
                    if (isClose) {
                        self.hide();
                    }
                
            });
                        
            jSearchInput.on("keyup", function() {
                
                self.lasTime = Date.now();
                
                if (jFiltersBlock.is(":visible")) {
                    self.hide();
                }
                
                setTimeout(function () {
                    var delta = Date.now() - self.lasTime;
                    if (delta > 500) {
                        jApplyBtn.click();
                    }
                }, 500);
            });
            
            jSearchInput.on("click", function() {
                if (jFiltersBlock.is(":visible")) {
                    self.hide();
                } else {
                    if (isNeedOpenFilter) {
                        self.show();
                    }
                }
            });
            
            
            if (jAppliedContainer.text() || jSearchInput.val()) {
                
                var jReset = $("<a>", {
                    'class': 'btn btn-sm btn-default',
                    'href': '#',
                    'title': "Сбросить все фильтры и поиск",
                    
                    'data-original-title': "Сбросить все фильтры и поиск",
                    'data-toggle': "tooltip",
                }).append("Сбросить всё");
                
                jReset.on("click", function() {
                    jSearchInput.val("");
                    $(".form-group", jFiltersBlock).trigger("reset");
                    jApplyBtn.click();
                    return false;
                });
                
                jAppliedContainer.append(jReset);
                
                
                
                jSaveBtn.show();
            }
            
            if (jSearchInput.val()) {
                var val = jSearchInput.val();
                var length = val.length;
                jSearchInput.val("");
                
                jSearchInput.selectionStart = length;
                jSearchInput.selectionEnd = length;
                jSearchInput.focus();
                jSearchInput.val(val);
            }

        },
        
        updateFastFilters: function() {
            var self = this;
            
            var jApplyBtn = $('.sx-filters-apply-btn', self.jWrapper);
            var jSaveBtn = $('.sx-save-values', self.jWrapper);
            var jAppliedContainer = $('.sx-applied-filters', self.jWrapper);
            var jFiltersBlock = $('.sx-filters-block', self.jWrapper);
            var jSearchInput = $('.sx-search-field input', self.jWrapper);
            
            $(".form-group", jFiltersBlock).removeClass("sx-applied");
            
            $(".form-group", jFiltersBlock).each(function() {
                    var jGroup = $(this);
                    var label = $("label", jGroup).text();
                    
                    //В поле есть выбор мода
                    var jModeSelect = $(".sx-filter-mode-wrapper select", jGroup);
                    var mode = null;
                    var modeValue = null;
                    if (jModeSelect.length) {
                        var jSelectModeWrapper = jModeSelect.closest('.sx-filter-mode-wrapper');
                        if (jSelectModeWrapper.data("default-mode") != jModeSelect.val()) {
                            
                            var mode = $("option:selected", jModeSelect).text();
                            var modeValue = jModeSelect.val();
                            
                        }
                    }
                    
                    var filterValueString = null;
                    
                    if ($("input", jGroup).length) {
                        $("input", jGroup).each(function() {
                            
                            if ($(this).val()) {
                                filterValueString = $(this).val();
                            }
                        });
                    } 
                    
                    if ($("select", jGroup).length) {
                        
                        $("select", jGroup).each(function() {
                            var jSelect = $(this);
                            
                            var jSelectModeWrapper = jSelect.closest('.sx-filter-mode-wrapper');
                            if (jSelectModeWrapper.length) {
                                
                            } else {
                                if ($("option:selected", jSelect).length) {

                                    var valueArray = [];
                                    $("option:selected", jSelect).each(function() {
                                        
                                        var value = $(this).attr('value');
                                        
                                        if (value) {
                                            valueArray.push($(this).text());
                                        }
                                    });
                                    
                                    if (valueArray.length) {
                                        filterValueString = valueArray.join(", ");
                                    }
                                    
                                }
                            }
                        });
                    }
                    
                    if (filterValueString || modeValue == 'empty' || modeValue == 'not_empty') {
                        
                        var fastLabel = null;
                        
                        if (modeValue == 'empty' || modeValue == 'not_empty') {
                            fastLabel = label + ': ' + mode;
                        } else {
                            if (mode) {
                                fastLabel = label + ': ' + mode + " " + filterValueString;
                            } else {
                                fastLabel = label + ': ' + filterValueString;
                            }
                        }
                        
                        var jFastCloseBtn = $("<span>", {
                            'class' : 'sx-close-fast-filter'
                        });
                        var jFast = $("<div>", {
                            'class': 'sx-fast-filter-btn',
                            'title': label,
                            'data-original-title': label,
                            'data-toggle': "tooltip",
                        }).append(fastLabel).append(jFastCloseBtn);
                        
                        jFastCloseBtn.on("click", function() {
                            jGroup.trigger("reset");
                            $(this).closest(".sx-fast-filter-btn").hide().remove();
                            jApplyBtn.click();
                            return false;
                        });
                        
                        jAppliedContainer.append(jFast);
                        
                        jGroup.addClass("sx-applied");
                        
                    }
                    
                    
                    
                    
                    
                });
            
        },
        
        show: function()
        {
            $('.sx-filters-block', self.jWrapper).slideDown();
        },
        
        hide: function()
        {
            $('.sx-filters-block', self.jWrapper).slideUp();
        }
    });
    
    new sx.classes.BackendFilters({$js});
})(sx, sx.$, sx._);
JS
);
?>

<?= \yii\helpers\Html::beginTag('div', $widget->wrapperOptions); ?>

<?

$activeFormClassName = \yii\helpers\ArrayHelper::getValue($widget->activeForm, 'class', \yii\widgets\ActiveForm::class);
\yii\helpers\ArrayHelper::remove($widget->activeForm, 'class');

?>


<?
$id = \Yii::$app->controller->action->backendShowing->id;
$editComponent = [
    'url' => \skeeks\cms\backend\helpers\BackendUrlHelper::createByParams([
        \skeeks\cms\backend\BackendComponent::getCurrent()->backendShowingControllerRoute.'/component-call-edit',
    ])
        ->merge([
            'id'                 => $id,
            'componentClassName' => $widget::className(),
            'callable_id'        => $widget->id."-edit",
        ])
        ->enableEmptyLayout()
        ->enableNoActions()
        ->url,
];
$editComponent = \yii\helpers\Json::encode($editComponent);
$callableDataInput = \yii\helpers\Html::textarea('callableData', base64_encode(serialize($widget->editData)), [
    'id'    => $widget->id."-edit",
    'style' => 'display: none;',
]);

?>

<?
/**
 * @var \skeeks\yii2\form\Builder $builder
 */
$form = $activeFormClassName::begin((array)$widget->activeForm);
?>

    <div class="sx-form-wrapper">

        <div class="d-flex">
            <div class="sx-search-field">
                <?php
                $fields = $builder->getFields();
                $searchField = \yii\helpers\ArrayHelper::getValue($fields, $widget->search_param_name);
                \yii\helpers\ArrayHelper::remove($fields, $widget->search_param_name);
                $builder->setFields($fields);

                $searchField->activeForm = $form;
                echo $searchField->run();
                ?>
                <div class="sx-search-field-btn-submit">
                    <button type="submit">
                    <i class="hs-admin-search g-absolute-centered"></i>
                    </button>
                </div>
            </div>
            <div class="sx-backend-filters-header">
                <div class="col-sm-12">
                    <!--<a href="#" onclick="return false;" style="text-decoration: none; border-bottom: 1px dashed;" class="sx-filters-toggle">
                        Фильтры
                    </a>-->
                    <span class="sx-controlls">
                    <?= \yii\helpers\Html::a('<i class="hs-admin-settings g-absolute-centered"></i>',
                        '#', [
                            'class'   => 'btn btn-sm sx-edit',
                            'onclick' => new \yii\web\JsExpression(<<<JS
            new sx.classes.backend.EditComponent({$editComponent}); return false;
JS
                            ),
                        ]); ?>
                </span>
                </div>
            </div>
        </div>

        <div class="sx-filters-applied-save">
            <div class="sx-applied-filters"></div>
            <div class="sx-save-filters-wrapper">
                <div>
                    <a class="btn btn-default btn-sm sx-save-values" title="Сохранить примененные значения" data-toggle="tooltip">
                        Сохранить примененное
                    </a>
                </div>
            </div>
        </div>

        <div class="sx-filters-block">
            <div class="d-flex sx-filters-block-inner" style="flex-direction: column;">

                <?
                $builder->setActiveForm($form);
                echo $builder->render();

                ?>
            </div>

            <div class="sx-edit-trigger" style=" position: absolute; right: 0px; bottom: 0;">

                <!--<a class="btn btn-secondary btn-sm float-right" data-toggle="dropdown" style="    background: silver;
        border-color: silver;"
                   href="#"
                   title="Добавить новый фильтр"
                >
    <span data-toggle="tooltip" title="Добавить новый фильтр">
                    <i class="fa fa-plus"></i>
                </span>

                </a>-->
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                    <div class="filter--group">
                        <div class="filter--group--body">
                            <div class="filter-search__input js-filter-search-hide" style="">
                                <input type="text" class="form-control" placeholder="Поиск фильтра">
                            </div>
                            <div class="filter--list">
                                <ul>
                                    <li>
                                        Фильтр 1
                                    </li>
                                    <li>
                                        Фильтр 2
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12" style="margin-top: 0.5rem;">
                <button class="btn btn-primary sx-filters-apply-btn" type="submit"><i class="glyphicon glyphicon-filter"></i> Применить</button>

                <a class="btn btn-default btn-sm float-right sx-filters-settings-btn" data-toggle="dropdown" style="    background: silver;
        border-color: silver;"
                   href="#"
                   title="Добавить новый фильтр"
                >
    <span data-toggle="tooltip" title="Добавить новый фильтр">
                    <i class="fa fa-plus"></i> Добавить еще фильтр
                </span>

                </a>

            </div>


            <!--<input type="hidden" value="1" name="<?/*= $widget->filtersSubmitKey; */?>">-->

        </div>

    </div>

<?
$form::end();
?>
<?php echo $callableDataInput; ?>

<?= \yii\helpers\Html::endTag('div'); ?>