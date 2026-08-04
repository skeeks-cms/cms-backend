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

$brandClasses = ['sx-shell-header__brand-link'];
if ($theme->logoSrcLight) {
    $brandClasses[] = 'sx-shell-header__brand--has-light-logo';
}
if ($theme->logoSrcDark) {
    $brandClasses[] = 'sx-shell-header__brand--has-dark-logo';
}

$brandContent = '';
$fallbackLogoSrc = $theme->logoSrc ?: ($theme->logoSrcLight ?: $theme->logoSrcDark);
if ($fallbackLogoSrc) {
    $brandContent .= Html::img($fallbackLogoSrc, [
        'class' => 'sx-shell-header__brand-logo sx-shell-header__brand-logo--fallback',
        'alt'   => $theme->logoTitle,
    ]);
}
if ($theme->logoSrcLight) {
    $brandContent .= Html::img($theme->logoSrcLight, [
        'class' => 'sx-shell-header__brand-logo sx-shell-header__brand-logo--light',
        'alt'   => $theme->logoTitle,
    ]);
}
if ($theme->logoSrcDark) {
    $brandContent .= Html::img($theme->logoSrcDark, [
        'class' => 'sx-shell-header__brand-logo sx-shell-header__brand-logo--dark',
        'alt'   => $theme->logoTitle,
    ]);
}
if (!$brandContent) {
    $brandContent = Html::tag('span', Html::encode($theme->logoTitle), [
        'class' => 'sx-shell-header__brand-title',
    ]);
}

$brand = Html::a(
    $brandContent,
    $theme->logoHref,
    [
        'class' => implode(' ', $brandClasses),
    ]
);

echo BackendShellHeaderWidget::widget([
    'brand'   => $brand,
    'actions' => BackendThemeModeSwitcher::widget([
        'containerClass' => 'sx-btn-backend-header',
    ]),
    'surfaceOptions' => array_merge([
        'class' => $theme->headerClasses,
    ], $theme->headerAppearanceAttributes),
    'navOptions' => [
        'aria-label' => \Yii::t('skeeks/cms', 'Backend navigation'),
    ],
]);
