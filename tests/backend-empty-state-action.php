<?php

function emptyStateActionExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$root = dirname(__DIR__).'/src';
$gridAction = file_get_contents($root.'/actions/BackendGridModelAction.php');
$relatedAction = file_get_contents($root.'/actions/BackendGridModelRelatedAction.php');

$reusePosition = strpos(
    $gridAction,
    '$backendAction = ArrayHelper::getValue($this->controller->actions, $actionId);'
);
$fallbackPosition = strpos(
    $gridAction,
    '$backendAction = $this->controller->createAction($actionId);'
);

emptyStateActionExpect(
    $reusePosition !== false
        && $fallbackPosition !== false
        && $reusePosition < $fallbackPosition,
    'Collection actions must reuse a controller-owned action before creating a fallback instance.'
);
emptyStateActionExpect(
    strpos($gridAction, '$defaultAction = $this->getBackendActionPresentation();') !== false
        && strpos($gridAction, '$emptyState[\'action\'] = $defaultAction;') !== false,
    'The default empty-state CTA must use the shared backend-action presentation.'
);
emptyStateActionExpect(
    strpos($relatedAction, "ArrayHelper::getValue(\$controller->actions, 'create')") !== false
        && strpos($relatedAction, '$createAction->url = ArrayHelper::merge') !== false,
    'Related collections must enrich the controller-owned create action with parent context.'
);

echo "Backend empty-state action contract: OK\n";
