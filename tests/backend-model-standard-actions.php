<?php

function standardActionsExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$controller = file_get_contents(dirname(__DIR__).'/src/controllers/BackendModelStandartController.php');
$controllerTrait = file_get_contents(dirname(__DIR__).'/src/controllers/TBackendModelController.php');
$entityLink = file_get_contents(dirname(__DIR__).'/src/widgets/BackendEntityLink.php');
$entityColumn = file_get_contents(dirname(__DIR__).'/src/grid/BackendEntityLinkColumn.php');
$viewAction = file_get_contents(dirname(__DIR__).'/src/actions/BackendModelViewAction.php');

standardActionsExpect(
    strpos($controller, 'BackendModelViewAction') === false,
    'BackendModelStandartController must not register a global view action.'
);
standardActionsExpect(
    strpos($entityLink, 'public $action;') !== false,
    'BackendEntityLink must not hard-code a default entity action.'
);
standardActionsExpect(
    strpos($entityLink, "'isRunFirstActionOnClick' => !\$this->action") !== false,
    'BackendEntityLink does not open the first available action by default.'
);
standardActionsExpect(
    strpos($controllerTrait, "ArrayHelper::multisort(\$this->_modelActions, 'priority')") !== false,
    'Backend model actions must remain ordered by priority.'
);
standardActionsExpect(
    strpos($entityColumn, 'public $action;') !== false
        && strpos($entityColumn, "'action'       => \$this->action") !== false,
    'BackendEntityLinkColumn does not expose and forward the entity action.'
);
standardActionsExpect(
    strpos($viewAction, 'class BackendModelViewAction') !== false,
    'The optional explicit read-only action must remain available.'
);

echo "Backend model standard action contract: OK\n";
