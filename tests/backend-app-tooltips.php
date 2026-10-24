<?php

function backendTooltipExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$backendApp = file_get_contents(dirname(__DIR__).'/src/assets/src/backend-app.js');
$tooltipInit = "\$('[data-toggle=\"tooltip\"]').tooltip();";
$initialInitPosition = strpos($backendApp, $tooltipInit);
$pjaxListenerPosition = strpos($backendApp, "$(document).on('pjax:complete'");

backendTooltipExpect($initialInitPosition !== false, 'Backend tooltip initialization is missing.');
backendTooltipExpect($pjaxListenerPosition !== false, 'Backend PJAX tooltip listener is missing.');
backendTooltipExpect($initialInitPosition < $pjaxListenerPosition, 'Backend tooltips are not initialized on initial page load.');
backendTooltipExpect(substr_count($backendApp, $tooltipInit) >= 2, 'Backend tooltips are not reinitialized after PJAX updates.');

echo "Backend app tooltip contract: OK\n";
