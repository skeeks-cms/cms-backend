<?php

function legacyFiltersSortableExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$root = dirname(__DIR__);
$widgets = [
    'SearchAndFiltersWidget' => file_get_contents($root.'/src/widgets/SearchAndFiltersWidget.php'),
    'FiltersWidgetV1' => file_get_contents($root.'/src/widgets/FiltersWidgetV1.php'),
];

foreach ($widgets as $name => $source) {
    legacyFiltersSortableExpect(
        strpos($source, 'BackendSortableAdapterAsset::register($this->view);') !== false
        && strpos($source, 'if ($canManageBackendShowings)') !== false,
        $name.' does not guard the Sortable adapter asset with the edit permission.'
    );
    legacyFiltersSortableExpect(
        strpos($source, "if (this.get('canManageBackendShowings'))") !== false
        && strpos($source, 'sx.backend.sortable.create(') !== false
        && strpos($source, 'itemSelector: "> .form-group"') !== false
        && strpos($source, 'onUpdate: function()') !== false,
        $name.' does not initialize the shared adapter for direct filter rows.'
    );
    legacyFiltersSortableExpect(
        strpos($source, '\\yii\\jui\\Sortable::widget()') === false
        && strpos($source, '.sortable(') === false,
        $name.' still uses jQuery UI Sortable directly.'
    );
}

echo "Backend legacy filters sortable adapter contract: OK\n";
