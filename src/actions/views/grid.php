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


<? if ($backendShowings = $action->backendShowings) : ?>
    <ul class="nav nav-tabs">
        <? foreach ($backendShowings as $backendShowing) : ?>
            <li class="sx-tab <?= $backendShowing->id == $action->backendShowing->id ? "active" : ""; ?>">
                <a href=#"><?= $backendShowing->displayName; ?></a>
            </li>
        <? endforeach; ?>
        <li>
            <a href="#w0-modal-create-filter" class="sx-btn-filter-create">
                <i class="glyphicon glyphicon-plus"></i>
            </a>
        </li>
    </ul>
<? endif; ?>

    <div class="tab-content">


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


<?php \skeeks\cms\widgets\Pjax::end(); ?>