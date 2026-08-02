<?php
/**
 * Default product-neutral backend header.
 *
 * Products normally override the brand and action slots by replacing this
 * view, while retaining BackendShellHeaderWidget as the common frame.
 *
 * @var yii\web\View $this
 */

use skeeks\cms\backend\assets\BackendShellHeaderAsset;
use skeeks\cms\backend\widgets\BackendShellHeaderWidget;
use skeeks\cms\backend\widgets\BackendThemeModeSwitcher;
use yii\helpers\Html;

$theme = $this->theme;
$headerAssetClass = $theme->headerAssetClass ?: BackendShellHeaderAsset::class;
$headerAssetClass::register($this);

$brand = Html::a(
    $theme->logoSrc
        ? Html::img($theme->logoSrc, [
            'class' => 'sx-shell-header__brand-logo',
            'alt'   => $theme->logoTitle,
        ])
        : Html::tag('span', Html::encode($theme->logoTitle), [
            'class' => 'sx-shell-header__brand-title',
        ]),
    $theme->logoHref,
    [
        'class' => 'sx-shell-header__brand-link',
    ]
);

echo BackendShellHeaderWidget::widget([
    'brand'   => $brand,
    'actions' => BackendThemeModeSwitcher::widget([
        'containerClass' => 'sx-btn-backend-header',
    ]),
    'surfaceOptions' => [
        'class' => $theme->headerClasses,
    ],
    'navOptions' => [
        'aria-label' => \Yii::t('skeeks/cms', 'Backend navigation'),
    ],
]);
