<?php
/**
 * Shared semantic sidebar menu slot.
 *
 * A product may override this view to choose another item source, while the
 * recursive markup remains owned by BackendShellMenuWidget.
 *
 * @var yii\web\View $this
 */

$theme = $this->theme;
$leftMenuAssetClass = $theme->leftMenuAssetClass
    ?: \skeeks\cms\backend\assets\BackendShellMenuAsset::class;
$leftMenuAssetClass::register($this);

echo \skeeks\cms\backend\widgets\BackendShellMenuWidget::widget();
