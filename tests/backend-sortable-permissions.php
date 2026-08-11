<?php

function backendSortablePermissionsExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$filtersWidget = file_get_contents(dirname(__DIR__).'/src/widgets/FiltersWidget.php');

backendSortablePermissionsExpect(
    strpos($filtersWidget, "'canManageBackendShowings' => \$canManageBackendShowings") !== false,
    'Filters widget does not expose the backend-showing permission to its client component.'
);
backendSortablePermissionsExpect(
    preg_match('/if \(\$canManageBackendShowings\) \{\s*BackendSortableAdapterAsset::register\(\$this->view\);\s*\}/', $filtersWidget) === 1,
    'Filters widget registers the Sortable adapter without the backend-showing permission guard.'
);
backendSortablePermissionsExpect(
    strpos($filtersWidget, '\\yii\\jui\\Sortable::widget()') === false,
    'Filters widget still registers the jQuery UI Sortable provider.'
);
backendSortablePermissionsExpect(
    strpos($filtersWidget, "if (this.get('canManageBackendShowings')) {") !== false,
    'Filters widget initializes Sortable without the backend-showing permission guard.'
);
backendSortablePermissionsExpect(
    strpos($filtersWidget, 'sx.backend.sortable.create(this.jSortable') !== false,
    'Filters widget does not use the shared backend sortable adapter.'
);
backendSortablePermissionsExpect(
    strpos($filtersWidget, 'this.jSortable.sortable(') === false,
    'Filters widget still initializes the jQuery UI provider directly.'
);

echo "Backend sortable permission contract: OK\n";
