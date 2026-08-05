<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */

use skeeks\cms\backend\helpers\BackendIcon;
use skeeks\cms\backend\widgets\BackendSectionHeader;

/* @var $this yii\web\View */
/* @var $action \skeeks\cms\backend\actions\BackendGridModelAction */
/* @var $backendShowing \skeeks\cms\backend\models\BackendShowing */
/* @var $controller \skeeks\crm\controllers\AdminCrmContactController */
$controller = $this->context;
?>
<?php $pjax = \skeeks\cms\widgets\Pjax::begin(); ?>

<?php $pageHeader = $action->pageHeaderConfig; ?>
<?php if ($pageHeader !== false) : ?>
    <?= BackendSectionHeader::widget((array)$pageHeader); ?>
<?php endif; ?>

<?
$backendCode = \skeeks\cms\backend\BackendComponent::getCurrent()->controllerPrefix;
$controllerRoute = \skeeks\cms\backend\BackendComponent::getCurrent()->backendShowingControllerRoute;
$showingsController = \Yii::$app->createController($controllerRoute)[0];
$showingsControllerTmp = clone $showingsController;
/**
 * @var \skeeks\cms\backend\BackendAction                      $actionIndex
 * @var \skeeks\cms\backend\BackendAction                      $actionCreate
 * @var \skeeks\cms\backend\controllers\BackendModelController $showingsController
 */
$actionCreate = \yii\helpers\ArrayHelper::getValue($showingsControllerTmp->actions, 'create');
$canCreateShowing = $action->backendShowings !== false
    && $actionCreate
    && \skeeks\cms\backend\BackendComponent::getCurrent()->canManageBackendShowings
    && $actionCreate->isVisible;


$backendShowing = new \skeeks\cms\backend\models\BackendShowing();
$backendShowing->loadDefaultValues();
$backendShowing->key = $action->backendShowingKey;


if ($canCreateShowing) {
    $createModal = \yii\bootstrap\Modal::begin([
    'id'     => 'sx-modal-create',
    'header' => '<b>'.\Yii::t('skeeks/backend', 'Создание отображения').'</b>',
    'footer' => '
        <button class="btn btn-primary" onclick="$(\'#sx-create-showing-form\').submit(); return false;">'.\Yii::t('skeeks/backend', 'Create').'</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">'.\Yii::t('skeeks/backend', 'Close').'</button>
    ',
]);
?>

<? $form = \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::begin([
    'id'             => 'sx-create-showing-form',
    'action'         => \yii\helpers\Url::to([$controllerRoute.'/create']),
    'validationUrl'  => \yii\helpers\Url::to([$controllerRoute.'/create', 'sx-validate' => true]),
    'clientCallback' => new \yii\web\JsExpression(<<<JS
    function (ActiveFormAjaxSubmit) {
        ActiveFormAjaxSubmit.on('success', function(e, response) {
            $("#sx-modal-create .close").click();
            _.delay(function() {
                window.location.reload();
            }, 300);
        });
    }
    
    /*'afterValidateCallback' => new \yii\web\JsExpression(<<<JS
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
        }*/
JS
    ),
]); ?>
<?= $form->field($backendShowing, 'name'); ?>
<?= $form->field($backendShowing, 'isPublic')->checkbox(\Yii::$app->formatter->booleanFormat); ?>
<?= $form->field($backendShowing, 'key')->hiddenInput()->label(false); ?>

<?= \yii\bootstrap\Html::hiddenInput('visibles'); ?>
<?= \yii\bootstrap\Html::hiddenInput('values'); ?>
    <button style="display: none;"></button>
<? \skeeks\cms\base\widgets\ActiveFormAjaxSubmit::end(); ?>

<? $createModal::end(); ?>
<? } ?>


<?
$action->backendShowing;
$backendShowings = $action->backendShowings;
$isDisplayBackendShowings = $action->isDisplayBackendShowings
    && $action->backendShowing
    && is_iterable($backendShowings)
    && (count((array)$backendShowings) > 1 || $canCreateShowing);
?>
<? if ($isDisplayBackendShowings) : ?>
    <?
    echo \skeeks\cms\backend\widgets\ContextMenuWidget::widget([
        'button'              => false,
        'items'               => [
            'no' => [
                'callback' => new \yii\web\JsExpression("function(key, options) {
                    $('a', $(this)).trigger('click');
                }"),
                //'onclick' => "$(this).click(); return false;",
                'name'     => 'Открыть',
            ],
        ],
        'rightClickSelectors' => ['.sx-no-active-tab'],
    ]);
    ?>
    <ul class="sx-backend-showing-tabs" role="list">
        <? foreach ($backendShowings as $backendShowing) : ?>
            <li class="sx-tab <?= $backendShowing->id == $action->backendShowing->id ? "active sx-active-tab" : "sx-no-active-tab"; ?>" id="sx-tab-<?= $backendShowing->id; ?>">
                <a href="<?= $action->getShowingUrl($backendShowing); ?>" class="sx-showing-tab <?= $backendShowing->id == $action->backendShowing->id ? "active" : ""; ?>">
                    <? if ($backendShowing->cms_user_id == \Yii::$app->user->id) : ?>
                        <img src="<?= \Yii::$app->user->identity->image ? \Yii::$app->user->identity->avatarSrc : \skeeks\cms\helpers\Image::getCapSrc(); ?>"
                             class="sx-avatar sx-avatar--sm"
                             title="Это представление видите только вы"
                        />
                    <? endif; ?>
                    <?= $backendShowing->displayName; ?>

                    <? if (
                        $backendShowing->id == $action->backendShowing->id
                        && \skeeks\cms\backend\BackendComponent::getCurrent()->canManageBackendShowings
                    ) : ?>

                        <?
                        $showingsController = \Yii::$app->createController($controllerRoute)[0];
                        $showingsController->setModel($backendShowing);

                        if ($showingsController->modelActions) {
                            echo \skeeks\cms\backend\widgets\ContextMenuControllerActionsWidget::widget([
                                'actions'             => (array)$showingsController->modelActions,
                                'isOpenNewWindow'     => true,
                                'rightClickSelectors' => ['#sx-tab-'.$backendShowing->id],
                                'button'              => [
                                    'class' => 'sx-showing-settings',
                                    'style' => 'cursor: pointer;',
                                    'tag'   => 'span',
                                    'label' => BackendIcon::render('settings', ['size' => 14]),
                                    'title' => \Yii::t('skeeks/backend', 'Настроить представление'),
                                    'aria-label' => \Yii::t('skeeks/backend', 'Настроить представление'),
                                ],
                            ]);
                        }


                        ?>
                    <? endif; ?>
                </a>
            </li>
        <? endforeach; ?>

        <? if ($canCreateShowing) : ?>
            <li class="sx-tab">
                <a href="#sx-modal-create" class="sx-showing-tab sx-btn-filter-create" data-toggle="modal" data-target="#sx-modal-create">
                    <?= BackendIcon::render('plus', ['size' => 18]); ?>
                </a>
            </li>
        <? endif; ?>
    </ul>
<? endif; ?>

    <div class="<?= $isDisplayBackendShowings ? "tab-content" : ""; ?>">
        <?
        $widgetClassName = $action->gridClassName;
        $widgetFiltersClassName = $action->filtersClassName;
        ?>
        <?
        $grid = $widgetClassName::begin((array)$action->gridConfig);
        $action->gridObject = $grid;
        ?>
        <?
        if ($widgetFiltersClassName && $action->shouldDisplayFilters($grid)) {
            $filtersConfig = (array)$action->filtersConfig;
            $filtersConfig['dataProvider'] = $grid->dataProvider;

            $component = $widgetFiltersClassName::begin($filtersConfig);
            $widgetFiltersClassName::end();
        }
        ?>

        <?
        $widgetClassName::end();
        ?>

        <? if (\Yii::$app->request->post('__gird-all-ids') == '__gird-all-ids') : ?>
            <?
            $query = $grid->dataProvider->query;
            ob_get_clean();
            $rr = new \skeeks\cms\helpers\RequestResponse();
            $pks = [];
            foreach ($query->each(100) as $element) {
                $pks[] = $element->{$controller->modelPkAttribute};
            }
            $rr->success = true;
            $rr->message = [
                'total' => count($pks),
                'pks'   => $pks,
            ];
            \Yii::$app->response->data = $rr;
            \Yii::$app->end();
            ?>
        <? endif; ?>

        <? if (YII_ENV === 'dev' && isset($grid->dataProvider->query)) : ?>
            <div style="margin-bottom: 10px;">
                <a href="#" onclick="$('.sx-grid-sql').toggle(); return false;" style="text-decoration: none; border-bottom: 1px dashed;">Показать SQL</a>
                <div class="sx-grid-sql" style="display: none; padding: 1px solid; margin-top: 10px;">
                    <code>
                        <pre style="margin-bottom: 0;"><?= $grid->dataProvider->query->createCommand()->rawSql; ?></pre>
                    </code>
                </div>
            </div>
        <? endif; ?>

    </div>


<?php $pjax::end(); ?>
