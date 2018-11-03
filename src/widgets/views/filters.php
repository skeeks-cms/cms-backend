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
    'id' => $widget->id,
    'isOpened' => $widget->isOpened,
]);

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
            
            if (self.isOpened) {
                $('.sx-form-wrapper', self.jWrapper).show();
            } else {
                $('.sx-form-wrapper', self.jWrapper).hide();
            }
            
            if (this.Cookie.get('opened') == 'y') {
                self.isOpened = true;
                $('.sx-form-wrapper', self.jWrapper).show();
            } 
            
            
            if (this.Cookie.get('opened') == 'n') {
                self.isOpened = false;
                $('.sx-form-wrapper', self.jWrapper).hide();
            } 
        },
        
        show: function()
        {
            $('.sx-form-wrapper', self.jWrapper).slideDown();
        },
        
        hide: function()
        {
            $('.sx-form-wrapper', self.jWrapper).slideUp();
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

    <div class="row sx-backend-filters-header">
        <!--<div class="sx-header-block" style="border-bottom: 1px solid #e2e2e2;">-->

            <div class="col-sm-12">
                <div class="col-sm-12">
                <a href="#" onclick="return false;" style="text-decoration: none; border-bottom: 1px dashed;" class="sx-filters-toggle">
                    Фильтры
                </a>

                <span class="sx-controlls">
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
                    <?= \yii\helpers\Html::a('<i class="fa fa-cog"></i>',
                        '#', [
                            'class'   => 'btn btn-sm sx-edit',
                            'onclick' => new \yii\web\JsExpression(<<<JS
            new sx.classes.backend.EditComponent({$editComponent}); return false;
JS
                            ),
                        ]).$callableDataInput; ?>
                </span>
            </div>
            </div>


    </div>

<div class="sx-form-wrapper" style="display: none;">
<?
$form = $activeFormClassName::begin((array)$widget->activeForm);

$builder->setActiveForm($form);
echo $builder->render();

?>
    <div class="row sx-form-buttons">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-5">
            <button class="btn btn-default btn-secondary" type="submit"><i class="glyphicon glyphicon-filter"></i> Применить</button>
            <button class="btn btn-default btn-secondary sx-save-values"><i class="fa fa-check"></i> Сохранить</button>
        </div>
        <div class="col-sm-2">
            <a class="btn btn-default btn-secondary btn-sm  sx-edit-trigger pull-right" href="#">
                <i class="fa fa-plus"></i>
            </a>
        </div>
        <div class="col-sm-2">

        </div>
    </div>
    <input type="hidden" value="1" name="<?= $widget->filtersSubmitKey; ?>">
<?
$form::end();
?>

</div>
<?= \yii\helpers\Html::endTag('div'); ?>