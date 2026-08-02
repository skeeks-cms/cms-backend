<?php
/**
 * Default context region before page content.
 *
 * @var yii\web\View $this
 */

$breadcrumbs = trim($this->render('@app/views/layouts/_breadcrumbs'));
?>
<?php if ($breadcrumbs !== '') : ?>
    <div class="sx-bg-gray-light sx-hide-on-empty sx-breadcrumbs-wrapper sx-shell-hidden-sm-down">
        <?= $breadcrumbs ?>
    </div>
<?php endif; ?>
