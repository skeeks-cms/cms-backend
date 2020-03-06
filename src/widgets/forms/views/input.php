<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.03.2016
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\backend\widgets\forms\NumberInputWidget */

$widget = $this->context;
$model = $widget->model;

if ($widget->maxInputWidth) {
    $this->registerCss(<<<CSS
#{$widget->wrapperId} input {
    max-width: {$widget->maxInputWidth};
}
CSS
);
}

?>

<?= \yii\helpers\Html::beginTag('div', $widget->wrapperOptions); ?>

<div class="d-flex flex-row sx-measure-row">
    <div class="my-auto sx-measure-base-value" style="">
        <div class="input-group">
            <? if ($widget->prepend) : ?>
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= $widget->prepend; ?></span>
                </div>
            <? endif; ?>
                <?= $element; ?>
            <? if ($widget->append) : ?>
                <div class="input-group-append">
                    <span class="input-group-text"><?= $widget->append; ?></span>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>


<?

$jsOptions = \yii\helpers\Json::encode($widget->clientOptions);


$this->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.NumberInputWidget = sx.classes.Component.extend({
        _onDomReady: function()
        {
            
        },
    });
    new sx.classes.NumberInputWidget({$jsOptions});
})(sx, sx.$, sx._);
JS
); ?>
<?= \yii\helpers\Html::endTag('div'); ?>