<?php

$assetRoot = dirname(__DIR__).'/src/widgets/assets/src';
$script = file_get_contents($assetRoot.'/js/ajax-controller-actions-widget.js');
$styles = file_get_contents($assetRoot.'/css/ajax-controller-actions-widget.css');

$checks = [
    "actions popover is attached to the drawer document body" => strpos($script, "'container': \"body\"") !== false,
    "actions popover uses its local viewport boundary" => strpos($script, "'boundary': 'viewport'") !== false,
    "actions popover chooses a side from available viewport space" => strpos($script, '_getPopoverPlacement') !== false
        && strpos($script, 'spaceOnRight') !== false
        && strpos($script, 'spaceOnLeft') !== false,
    "actions popover remains viewport-safe on narrow screens" => strpos($styles, 'min-width: min(220px, calc(100vw - 16px));') !== false,
    "actions menu owns its vertical layout without Bootstrap utilities" => strpos($styles, 'flex-direction: column;') !== false
        && strpos($styles, 'align-items: stretch;') !== false,
];

$failed = [];
foreach ($checks as $label => $passed) {
    if (!$passed) {
        $failed[] = $label;
    }
}

if ($failed) {
    fwrite(STDERR, "Ajax controller actions widget contract failed:\n- ".implode("\n- ", $failed)."\n");
    exit(1);
}

echo "Ajax controller actions widget contract: OK\n";
