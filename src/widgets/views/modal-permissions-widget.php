<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 21.03.2017
 */
/* @var $this yii\web\View */
/* @var $widget \skeeks\cms\backend\widgets\ModalPermissionWidget */
$widget = $this->context;
$controller = $widget->controller;
?>

<? if ($controller instanceof \skeeks\cms\IHasPermissions && $controller->permissionNames) : ?>
    <? foreach ($controller->permissionNames as $parmissionName => $permissionLabel) : ?>
        <?= \skeeks\cms\rbac\widgets\adminPermissionForRoles\AdminPermissionForRolesWidget::widget([
            'permissionName'        => $parmissionName,
            'permissionDescription' => $permissionLabel . " — " . $parmissionName,
            'label'                 => $permissionLabel,
        ]); ?>
        <!--<p><?/*= $parmissionName; */?></p>-->
    <? endforeach; ?>

    <? if ($controller->allActions) : ?>
        <hr />
        <? foreach ($controller->allActions as $actionObj) : ?>
            <div class="row sx-action-permission">
                <div class="col-md-10 col-md-offset-1">
                    <? if ($actionObj instanceof \skeeks\cms\IHasPermissions && $actionObj->permissionNames) : ?>
                        <? foreach ($actionObj->permissionNames as $parmissionName => $permissionLabel) : ?>
                            <?= \skeeks\cms\rbac\widgets\adminPermissionForRoles\AdminPermissionForRolesWidget::widget([
                                'permissionName'        => $parmissionName,
                                'permissionDescription' => $permissionLabel . " — " . $parmissionName,
                                'label'                 => $actionObj->name,
                            ]); ?>
                            <!--<small><?/*= $parmissionName; */?> (<?/*= $permissionLabel; */?>)</small>-->
                        <? endforeach; ?>
                    <? endif; ?>
                </div>
            </div>
        <? endforeach; ?>
    <? endif; ?>
<? endif; ?>
