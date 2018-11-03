<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 27.03.2015
 *
 * @var $component \skeeks\yii2\config\ConfigComponent
 * @var $message string
 */
/* @var $this yii\web\View */

?>

<? $model = $component->configModel; ?>

<?php $form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin([
    'enableAjaxValidation'   => false,
    'enableClientValidation' => false,
]); ?>


<?
if ($deleted) {

    $this->registerJs(<<<JS
    window.location.reload();

JS
    );
}
?>

<? if ($error) : ?>
    <?= \yii\bootstrap\Alert::widget([
        'body'    => $error,
        'options' => [
            'class' => 'alert-danger',
        ],
    ]); ?>
<? endif; ?>
<? if ($success) : ?>
    <?= \yii\bootstrap\Alert::widget([
        'body'    => $success,
        'options' => [
            'class' => 'alert-success',
        ],
    ]); ?>
<? endif; ?>
<?= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
    [$model], $model->builderModels()
)); ?>

<? if ($fields = $model->builderFields()) : ?>
    <?
    echo (new \skeeks\yii2\form\Builder([
        'models'     => $model->builderModels(),
        'model'      => $model,
        'activeForm' => $form,
        'fields'     => $model->builderFields(),
    ]))->render(); ?>
<? else : ?>
    Нет редактируемых настроек для данного компонента
<? endif; ?>

<?= $form->buttonsStandart($model); ?>
<?= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
    [$model], $model->builderModels()
)); ?>

<? if ($component->configStorage->exists($component->configBehavior)) : ?>
    <div class="row">
        <div class="col-md-12">
            <button href="#" class="btn btn-danger btn-xs pull-right sx-btn-remove">
                <span class="fa fa-trash"></span> Удалить настройки
            </button>
        </div>
    </div>

    <?


    $this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.Remove = sx.classes.Component.extend({
    
        _init: function()
        {
            
        },
        
        _onDomReady: function()
        {
            $(".sx-btn-remove").on("click", function() {
                var jForm = $(this).closest('form');
                jForm.append(
                    $("<input>", {
                        'name' : 'delete',
                        'type' : 'hidden',
                        'value' : 'true'
                    })
                ).submit();
                
                return false;
            });
        },
        
        _onWindowReady: function()
        {}
    });
    
    new sx.classes.Remove();
})(sx, sx.$, sx._);
JS
    );
    ?>
<? endif; ?>

<?php \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end(); ?>
