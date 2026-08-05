<?php

$styles = file_get_contents(dirname(__DIR__).'/src/assets/src/cabinet.css');

$requiredSelectors = [
    '.sx-shell-header__surface--client',
    '#sideNav.sx-shell-sidebar--client',
    '.sx-shell-help',
    '.sx-shell-footer',
];

$forbiddenSelectors = [
    '.sx-content-wrapper h1',
    '.grid-view:not(.sx-backend-grid)',
    '.sx-content-wrapper .pagination',
    '.sx-content-model-actions .sx-nav-model',
    'form.sx-backend-form',
    '.sx-panel__header',
    '.sx-cabinet-service-status',
    '.sx-monitor-summary',
    '.sx-filter-row',
];

$failed = [];
foreach ($requiredSelectors as $selector) {
    if (strpos($styles, $selector) === false) {
        $failed[] = "missing cabinet-owned selector: {$selector}";
    }
}

foreach ($forbiddenSelectors as $selector) {
    if (strpos($styles, $selector) !== false) {
        $failed[] = "standard backend selector leaked into cabinet.css: {$selector}";
    }
}

if ($failed) {
    fwrite(STDERR, "Backend cabinet asset contract failed:\n- ".implode("\n- ", $failed)."\n");
    exit(1);
}

echo "Backend cabinet asset contract: OK\n";
