<?php

$widget = file_get_contents(dirname(__DIR__).'/src/widgets/BackendEntityLink.php');

function backendEntityLinkAccessExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

backendEntityLinkAccessExpect(strpos($widget, 'public $checkAccess = true;') !== false, 'Entity links do not check access by default.');
backendEntityLinkAccessExpect(strpos($widget, "ltrim(\$this->controllerId, '/')") !== false, 'Entity links do not derive the controller permission.');
backendEntityLinkAccessExpect(strpos($widget, "return Html::tag('span', \$content, \$options);") !== false, 'Denied entity links do not render a non-interactive fallback.');
backendEntityLinkAccessExpect(strpos($widget, "unset(\$options['href'], \$options['onclick'], \$options['data']);") !== false, 'Denied entity links retain interactive attributes.');

echo "Backend entity-link access contract: OK\n";
