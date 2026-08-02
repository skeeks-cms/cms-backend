<?php
/**
 * Common permission modal for controllers that expose permission settings.
 *
 * @var yii\web\View $this
 */

use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\backend\widgets\ModalPermissionWidget;
use skeeks\cms\IHasPermissions;

$backendUrl = BackendUrlHelper::createByParams()->setBackendParamsByCurrentRequest();
$controller = \Yii::$app->controller;
?>
<?php if (
    !$backendUrl->isEmptyLayout
    && $controller instanceof IHasPermissions
    && \Yii::$app->user->can('rbac/admin-permission')
) : ?>
    <?= ModalPermissionWidget::widget([
        'id'                   => 'sx-permisson-modal',
        'controller'           => $controller,
        'toggleButton'         => false,
        'standartToggleButton' => false,
    ]) ?>
<?php endif; ?>
