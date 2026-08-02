<?php
/**
 * Product-neutral semantic breadcrumbs.
 *
 * Product-specific hierarchy belongs in controller breadcrumbsData or in a
 * product view override. Do not add hosting/CRM branches here.
 *
 * @var yii\web\View $this
 */

use skeeks\cms\backend\helpers\BackendIcon;
use skeeks\cms\backend\IHasBreadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$controller = \Yii::$app->controller;
if (!$controller instanceof IHasBreadcrumbs || !$controller->breadcrumbsData) {
    return;
}

$items = array_merge([
    [
        'label' => \Yii::t('skeeks/cms', 'Main'),
        'url'   => $this->theme->logoHref,
    ],
], $controller->breadcrumbsData);
?>
<nav class="sx-breadcrumbs-nav" aria-label="<?= Html::encode(\Yii::t('skeeks/cms', 'Breadcrumbs')) ?>">
    <ol class="sx-breadcrumbs">
        <?php foreach ($items as $index => $item) : ?>
            <?php
            $label = (string) ArrayHelper::getValue($item, 'label');
            $url = ArrayHelper::getValue($item, 'url');
            $isLast = $index === count($items) - 1;
            ?>
            <li class="sx-breadcrumbs__item<?= $isLast ? ' is-current' : '' ?>">
                <?php if ($url && !$isLast) : ?>
                    <?= Html::a(Html::encode($label), $url, [
                        'class' => 'sx-breadcrumbs__link',
                    ]) ?>
                <?php else : ?>
                    <span class="sx-breadcrumbs__label"<?= $isLast ? ' aria-current="page"' : '' ?>>
                        <?= Html::encode($label) ?>
                    </span>
                <?php endif; ?>

                <?php if (!$isLast) : ?>
                    <?= BackendIcon::render('chevron-right', [
                        'size'  => 14,
                        'class' => 'sx-breadcrumbs__separator',
                    ]) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
