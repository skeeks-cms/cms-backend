<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\backend\IHasInfoActions;
use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\backend\themes\BackendTheme;
use skeeks\cms\backend\widgets\BackendShellSidebarWidget;
use skeeks\cms\backend\widgets\ControllerActionsWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $theme BackendTheme */

$theme = isset($theme) ? $theme : $this->theme;
$appAssetClass = $theme->appAssetClass
    ?: \skeeks\cms\backend\assets\BackendAppAsset::class;
$appAssetClass::register($this);
\skeeks\cms\backend\assets\BackendShellHeaderAsset::register($this);
\skeeks\cms\backend\assets\BackendShellMenuAsset::register($this);
$paletteCss = $theme->paletteCss;
if ($paletteCss !== '') {
    $this->registerCss($paletteCss, ['id' => 'sx-backend-theme-palette']);
}
\skeeks\cms\widgets\user\UserOnlineTriggerWidget::widget();

$themeMode = $theme->normalizedThemeMode;
$themeModeStorageKey = (string) $theme->themeModeStorageKey;
$backendUrl = BackendUrlHelper::createByParams()->setBackendParamsByCurrentRequest();
$isEmpty = $backendUrl->isEmptyLayout;

if ($isEmpty && \Yii::$app->getModule('debug')) {
    \Yii::$app->getModule('debug')->panels = [];
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html
    lang="<?= Yii::$app->language ?>"
    prefix="og: http://ogp.me/ns#"
    data-sx-shell-layout="backend"
    data-sx-theme="<?= Html::encode($themeMode === 'dark' ? 'dark' : 'light') ?>"
    data-sx-theme-mode="<?= Html::encode($themeMode) ?>"
    data-sx-theme-storage-key="<?= Html::encode($themeModeStorageKey) ?>"
>
<head>
    <?= $this->render('@skeeks/cms/backend/views/layouts/_theme-mode-bootstrap', [
        'theme' => $theme,
    ]) ?>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="has-fixed-sidebar<?= $isEmpty ? ' sx-empty' : '' ?>">
<?php $this->beginBody() ?>

<?php if (!$isEmpty) : ?>
    <?= $this->render('@app/views/layouts/_header') ?>
<?php endif; ?>

<main class="sx-main">
    <?= $this->render('@app/views/layouts/_container-begin') ?>
    <div class="sx-main-wrapper">
        <?php if (!$isEmpty) : ?>
            <?= BackendShellSidebarWidget::widget([
                'beforeMenu' => $this->render('@app/views/layouts/_before-menu'),
                'menu'       => $this->render('@app/views/layouts/_menu'),
                'afterMenu'  => $this->render('@app/views/layouts/_after-menu'),
                'options'    => [
                    'class' => $theme->slideNavClasses,
                ],
            ]) ?>
        <?php endif; ?>

        <div class="sx-main-col">
            <?= $this->render('@app/views/layouts/_before-content') ?>

            <div class="sx-content-wrapper">
                <div class="sx-content-actions">
                    <?php if (!$backendUrl->isNoActions) : ?>
                        <?php if (
                            (isset(\Yii::$app->controller->modelActions)
                                && !\Yii::$app->controller->modelActions)
                            || !isset(\Yii::$app->controller->modelActions)
                        ) : ?>
                            <?php if (
                                \Yii::$app->controller
                                && \Yii::$app->controller instanceof IHasInfoActions
                                && \Yii::$app->controller->actions
                            ) : ?>
                                <?= ControllerActionsWidget::currentWidget([
                                    'options' => [
                                        'class' => 'nav sx-main-page-nav',
                                        'style' => 'font-size: 16px;',
                                    ],
                                    'itemWrapperOptions' => [
                                        'class' => 'nav-item',
                                    ],
                                    'itemOptions' => [
                                        'class' => 'nav-link',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if (
                    \Yii::$app->controller
                    && \Yii::$app->controller instanceof IBackendModelController
                    && \Yii::$app->controller->modelActions
                ) : ?>
                    <?php if (!$backendUrl->isNoModelActions) : ?>
                        <div class="sx-content-model-actions">
                            <div class="panel-content-before panel-content-before-second">
                                <div
                                    class="sx-model-title"
                                    title="<?= Html::encode(\Yii::$app->controller->modelShowName) ?>"
                                >
                                    <?= \Yii::$app->controller->modelHeader ?>
                                </div>

                                <?php if (count(\Yii::$app->controller->modelActions) > 1) : ?>
                                    <div class="sx-nav-model-wrapper" data-axis="x">
                                        <?php
                                        $modelActions = \Yii::$app->controller->modelActions;
                                        ArrayHelper::remove($modelActions, 'delete');
                                        ArrayHelper::remove($modelActions, 'copy');

                                        echo ControllerActionsWidget::widget([
                                            'actions'            => $modelActions,
                                            'activeId'           => \Yii::$app->controller->action->id,
                                            'options'            => [
                                                'class' => 'sx-nav-model',
                                            ],
                                            'itemWrapperOptions' => [
                                                'class' => 'sx-nav-model__item',
                                            ],
                                            'itemOptions'        => [
                                                'class' => 'sx-nav-model__link',
                                            ],
                                        ]);
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="tab-content">
                        <section>
                            <?= $content ?>
                        </section>
                    </div>
                <?php else : ?>
                    <section>
                        <?= $content ?>
                    </section>
                <?php endif; ?>
            </div>

            <?= $this->render('@app/views/layouts/_footer') ?>
        </div>
    </div>
</main>

<?= $this->render('@app/views/layouts/_modals') ?>
<?php if (!$isEmpty) : ?>
    <?= $this->render('@app/views/layouts/_quick-access') ?>
<?php endif; ?>
<?= $this->render('@app/views/layouts/_end-body') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
