<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 18.03.2018
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\queryfilters\QueryFiltersWidget */
$widget = $this->context;
$fields = $widget->filtersModel->builderFields();
?>

<?= \yii\helpers\Html::beginTag('div', $widget->wrapperOptions); ?>

<?

$activeFormClassName = \yii\helpers\ArrayHelper::getValue($widget->activeForm, 'class', \yii\widgets\ActiveForm::class);
\yii\helpers\ArrayHelper::remove($widget->activeForm, 'class');

?>

    <div class="row sx-backend-filters-header">
        <div class="col-sm-12">
            <!--<div class="sx-header-block" style="border-bottom: 1px solid #e2e2e2;">-->
            <div class="col-sm-3">
                <div class="sx-title">Фильтры</div>
            </div>
            <div class="col-sm-6">

            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="sx-controlls pull-right">

                        <?
                        $id = \Yii::$app->controller->action->backendShowing->id;
                        $editComponent = [
                            'url' => \skeeks\cms\backend\helpers\BackendUrlHelper::createByParams([
                                \skeeks\cms\backend\BackendComponent::getCurrent()->backendShowingControllerRoute . '/component-call-edit'
                            ])
                                ->merge([
                                    'id'  => $id,
                                    'componentClassName'  => $widget::className(),
                                    'callable_id'         => $widget->id . "-edit",
                                ])
                                ->enableEmptyLayout()
                                ->enableNoActions()
                                ->url
                        ];
                        $editComponent = \yii\helpers\Json::encode($editComponent);
                        $callableDataInput = \yii\helpers\Html::textarea('callableData', base64_encode(serialize($widget->editData)), [
                            'id' => $widget->id . "-edit",
                            'style' => 'display: none;'
                        ]);

                        ?>
                        <?= \yii\helpers\Html::a('<i class="fa fa-cog"></i>',
                '#', [
                    'class' => 'btn btn-sm sx-edit',
                    'onclick' => new \yii\web\JsExpression(<<<JS
            new sx.classes.backend.EditComponent({$editComponent}); return false;
JS
                    )
                ]) . $callableDataInput; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?
$form = $activeFormClassName::begin((array)$widget->activeForm);

$builder->setActiveForm($form);
echo $builder->render();

?>
    <div class="row sx-form-buttons">
        <div class="col-sm-12">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-5">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-filter"></i> Применить</button>
                <button class="btn btn-default sx-save-values"><i class="fa fa-check"></i> Сохранить</button>
            </div>
            <div class="col-sm-2">
                <a class="btn btn-default btn-sm dropdown-toggle sx-edit-trigger pull-right" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
            <div class="col-sm-2">

            </div>
        </div>
    </div>
<input type="hidden" value="1" name="<?= $widget->filtersSubmitKey; ?>">
<?
$activeFormClassName::end();
?>
<?= \yii\helpers\Html::endTag('div'); ?>