<?php

$guide = file_get_contents(dirname(__DIR__).'/BACKEND_UI_GUIDE.md');
$readme = file_get_contents(dirname(__DIR__).'/README.md');

function guideExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$requiredContracts = [
    'BackendGridModelAction',
    'PRESENTATION_PAGE',
    'BackendSectionHeader',
    'GridViewWidget',
    'ListViewWidget',
    'BackendEntityLinkColumn',
    'BackendEntityLink::widget()',
    'sx-collection-cell__primary',
    'sx-surface',
    'sx-block',
    'sx-panel',
    'BackendPanelAsset',
    'BackendUiAsset',
    'ASSET_BUDGET.md',
    'light and dark',
    'horizontal overflow',
    'DefaultActionColumn',
    'cms-theme-unify-v2',
    'Vendor boundary',
];

foreach ($requiredContracts as $contract) {
    guideExpect(stripos($guide, $contract) !== false, "Backend UI guide is missing {$contract}.");
}

guideExpect(
    strpos($readme, '[`BACKEND_UI_GUIDE.md`](BACKEND_UI_GUIDE.md)') !== false,
    'README does not link the canonical Backend UI guide.'
);
guideExpect(
    strpos($guide, "'icon' => 'folder'") !== false,
    'Guide page-header example must use a supported semantic icon.'
);
guideExpect(
    strpos($guide, 'Do not commit or push without approval.') !== false,
    'Guide is missing the safe Git boundary.'
);

echo "Backend UI guide contract: OK\n";
