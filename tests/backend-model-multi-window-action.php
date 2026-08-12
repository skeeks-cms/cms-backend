<?php

function multiWindowExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$root = dirname(__DIR__).'/src';
$action = file_get_contents($root.'/actions/BackendModelMultiWindowAction.php');
$gridAction = file_get_contents($root.'/actions/BackendGridModelAction.php');
$relatedAction = file_get_contents($root.'/actions/BackendGridModelRelatedAction.php');
$gridAsset = file_get_contents($root.'/actions/assets/BackendGridModelActionAsset.php');
$multiAsset = file_get_contents($root.'/actions/assets/BackendGridModelMultiActionAsset.php');
$windowAsset = file_get_contents($root.'/actions/assets/BackendModelMultiWindowActionAsset.php');
$script = file_get_contents($root.'/actions/assets/src/multi-window-action.js');

multiWindowExpect(
    strpos($action, 'class BackendModelMultiWindowAction') !== false
        && strpos($action, 'implements IHasActiveForm') !== false,
    'The parameterized multi-action must use the standard backend form contract.'
);
multiWindowExpect(
    strpos($action, 'generateRandomString(32)') !== false
        && strpos($action, 'getSelectionCacheKey') !== false
        && strpos($action, 'Yii::$app->cache->set') !== false
        && strpos($action, 'if (!$isStored)') !== false,
    'Selected primary keys must be represented by an opaque, user-bound server token.'
);
multiWindowExpect(
    strpos($action, 'The selection has expired. Close this window') !== false
        && strpos($action, "'sx-surface sx-surface--padded'") !== false,
    'An expired selection must render a recoverable iframe state.'
);
multiWindowExpect(
    strpos($action, "->enableEmptyLayout()") !== false
        && strpos($action, "->enableNoActions()") !== false,
    'The prepared action URL must use the canonical backend iframe layout.'
);
multiWindowExpect(
    strpos($action, '$requestContext = \Yii::$app->request->get();') !== false
        && strpos($action, 'ArrayHelper::merge($urlData, $requestContext)') !== false,
    'The iframe URL must retain related collection context from the preparation request.'
);
multiWindowExpect(
    strpos($action, "'type' => 'update'") !== false
        && strpos($action, "'submitBtn'") !== false,
    'Successful bulk updates must emit the standard model-update lifecycle.'
);
multiWindowExpect(
    strpos($action, "if (\$this->buttons === ['apply'])") !== false,
    'A domain action must be able to replace the default apply/save button set.'
);
multiWindowExpect(
    strpos($action, 'Yii::$app->db->transaction') !== false,
    'Parameterized bulk updates must be transactional by default.'
);
multiWindowExpect(
    strpos($script, 'sx.classes.grid.MultiWindowAction') !== false
        && strpos($script, 'sx.classes.backend.widgets.Action') !== false,
    'The grid bridge must open a standard BackendAction window.'
);
multiWindowExpect(
    strpos($gridAsset, "'multi-window-action.js'") === false
        && strpos($gridAsset, 'GridViewStandartAsset') === false,
    'A grid without multi-actions must not load any multi-action runtime.'
);
multiWindowExpect(
    strpos($multiAsset, 'GridViewStandartAsset') !== false
        && strpos($windowAsset, "'multi-window-action.js'") !== false
        && strpos($windowAsset, 'BackendGridModelMultiActionAsset::class') !== false
        && strpos($action, 'BackendModelMultiWindowActionAsset::register($grid->view)') !== false,
    'The iframe bridge must load only for a registered multi-window action and after the base multi-action runtime.'
);
multiWindowExpect(
    strpos($gridAction, 'BackendGridModelMultiActionAsset::register($gridViewWidget->view)')
        > strpos($gridAction, 'if (!$multiActions)'),
    'The base multi-action asset must be registered only after a non-empty action set is confirmed.'
);
multiWindowExpect(
    strpos($relatedAction, 'passRelationContextToMultiWindowActions') !== false
        && strpos($relatedAction, 'instanceof BackendModelMultiWindowAction') !== false
        && strpos($relatedAction, '$controller->modelMultiActions') !== false
        && strpos($relatedAction, '$relationContext') !== false,
    'A related grid must pass its bound parent context to parameterized iframe multi-actions.'
);

echo "Backend model multi-window action contract: OK\n";
