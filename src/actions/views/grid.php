<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */
/* @var $this yii\web\View */
/* @var $action \skeeks\cms\backend\actions\BackendGridModelAction */
/* @var $backendShowing \skeeks\cms\backend\models\BackendShowing */
$controller = $this->context;
$action = $controller->action;
?>
<?php $pjax = \skeeks\cms\widgets\Pjax::begin(); ?>
<?
$backendCode = \skeeks\cms\backend\BackendComponent::getCurrent()->controllerPrefix;
$controllerRoute = \skeeks\cms\backend\BackendComponent::getCurrent()->backendShowingControllerRoute;
$showingsController = \Yii::$app->createController($controllerRoute)[0];
$showingsControllerTmp = clone $showingsController;
/**
 * @var \skeeks\cms\backend\BackendAction $actionIndex
 * @var \skeeks\cms\backend\BackendAction $actionCreate
 * @var \skeeks\cms\backend\controllers\BackendModelController $showingsController
 */
$actionCreate = \yii\helpers\ArrayHelper::getValue($showingsControllerTmp->actions, 'create');


$backendShowing = new \skeeks\cms\backend\models\BackendShowing();
$backendShowing->loadDefaultValues();
$backendShowing->key = $action->uniqueId;



$createModal = \yii\bootstrap\Modal::begin([
    'id'        => 'sx-modal-create',
    'header'    => '<b>' . \Yii::t('skeeks/backend', 'Создание отображения') . '</b>',
    'footer'    => '
        <button class="btn btn-primary" onclick="$(\'#sx-create-showing-form\').submit(); return false;">' . \Yii::t('skeeks/backend', 'Create') . '</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">' . \Yii::t('skeeks/backend', 'Close') . '</button>
    ',
]);
?>

    <? $form = \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::begin([
            'id'                => 'sx-create-showing-form',
            'action'            => \yii\helpers\Url::to([ $controllerRoute . '/create']),
            'validationUrl'     => \yii\helpers\Url::to([ $controllerRoute . '/create', 'sx-validate' => true]),
            'afterValidateCallback'     => new \yii\web\JsExpression(<<<JS
        function(jForm, AjaxQuery)
        {
            var Handler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery);
            var Blocker = new sx.classes.AjaxHandlerBlocker(AjaxQuery, {
                'wrapper' : jForm.closest('.modal-content')
            });

            Handler.bind('success', function()
            {
                _.delay(function()
                {
                    window.location.reload();
                }, 1000);
            });
        }
JS
            )
        ]); ?>
        <?= $form->field($backendShowing, 'name'); ?>
        <?= $form->field($backendShowing, 'isPublic')->checkbox(\Yii::$app->formatter->booleanFormat); ?>
        <?= $form->field($backendShowing, 'key')->hiddenInput()->label(false); ?>

        <?= \yii\bootstrap\Html::hiddenInput('visibles'); ?>
        <?= \yii\bootstrap\Html::hiddenInput('values'); ?>
        <button style="display: none;"></button>
    <? \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::end(); ?>

<? \yii\bootstrap\Modal::end();?>



<?
$action->backendShowing;
$backendShowings = $action->backendShowings;
?>
<? if ($backendShowings && $action->backendShowing) : ?>
    <ul class="nav nav-tabs sx-backend-showing-tabs">
        <? foreach ($backendShowings as $backendShowing) : ?>
            <li class="sx-tab <?= $backendShowing->id == $action->backendShowing->id ? "active" : ""; ?>">
                <a href="<?= $action->getShowingUrl($backendShowing); ?>"><?= $backendShowing->displayName; ?></a>
            </li>
        <? endforeach; ?>

        <? if ($actionCreate) : ?>
            <li>
                <a href="#sx-modal-create" class="sx-btn-filter-create" data-toggle="modal" data-target="#sx-modal-create">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </li>
        <? endif; ?>
    </ul>
<? endif; ?>

    <div class="tab-content">

        <div class="row">
            <div class="col-md-12">
                <div class="pull-left">
                    <?
                        $showingsControllerTmp = clone $showingsController;
                        $showingsControllerTmp->setModel($action->backendShowing);

                        echo \skeeks\cms\backend\widgets\DropdownControllerActionsWidget::widget([
                            'actions' => $showingsControllerTmp->modelActions,
                            'isOpenNewWindow' => true,
                            'renderFirstAction' => false
                        ]);
                    ?>
                </div>
            </div>
        </div>

        <?
        $widgetClassName = $action->gridClassName;
        $widgetFiltersClassName = $action->filtersClassName;
        ?>

        <?
        /**
         *
         */
        $grid = $widgetClassName::begin((array)$action->gridConfig);
        ?>

        <?
        $filtersConfig = (array)$action->filtersConfig;
        $filtersConfig['dataProvider'] = $grid->dataProvider;

        $component = $widgetFiltersClassName::begin($filtersConfig);
        $widgetFiltersClassName::end();
        ?>


        <!--
<? /*$component = $component->configModel; */ ?>
<?php /*$form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin(); */ ?>

<? /*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
)); */ ?>

<? /* if ($fields = $component->builderFields()) : */ ?>
    <? /* echo (new \skeeks\yii2\form\Builder([
        'models'     => $component->builderModels(),
        'model'      => $component,
        'activeForm' => $form,
        'fields'     => $fields,
    ]))->render(); */ ?>
<? /* elseif ($formContent = $component->renderConfigForm($form)) : */ ?>
    <? /*= $formContent; */ ?>
<? /* else : */ ?>
    Нет редактируемых настроек для данного компонента
<? /* endif; */ ?>

<? /*= $form->buttonsStandart($component); */ ?>
<? /*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
)); */ ?>

--><?php /*\skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end(); */ ?>


        <?
        $widgetClassName::end();
        ?>
        <? if (YII_ENV === 'dev') : ?>
            <pre><?= $component->dataProvider->query->createCommand()->rawSql; ?></pre>
        <? endif; ?>

    </div>



<?/* $component = $grid->configModel;  */?><!--
<?php /*$form = \skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::begin();  */?>

<?/*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
));  */?>

<?/*  if ($fields = $component->builderFields()) :  */?>
    <?/*  echo (new \skeeks\yii2\form\Builder([
        'models'     => $component->builderModels(),
        'model'      => $component,
        'activeForm' => $form,
        'fields'     => $fields,
    ]))->render();  */?>
<?/*  elseif ($formContent = $component->renderConfigForm($form)) :  */?>
    <?/*= $formContent;  */?>
<?/*  else :  */?>
    Нет редактируемых настроек для данного компонента
<?/*  endif;  */?>

<?/*= $form->buttonsStandart($component);  */?>
<?/*= $form->errorSummary(\yii\helpers\ArrayHelper::merge(
        [$component], $component->builderModels()
));  */?>

--><?php /*\skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab::end();  */?>





<?php \skeeks\cms\widgets\Pjax::end(); ?>