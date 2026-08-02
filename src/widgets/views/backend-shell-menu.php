<?php
/**
 * @var yii\web\View $this
 * @var skeeks\cms\backend\BackendMenuItem[] $items
 * @var array $options
 * @var bool $activeBranchesOnly
 */

use yii\helpers\Html;

echo Html::beginTag('ul', $options);
echo $this->render('backend-shell-menu-items', [
    'items'              => $items,
    'level'              => 1,
    'activeBranchesOnly' => $activeBranchesOnly,
]);
echo Html::endTag('ul');
