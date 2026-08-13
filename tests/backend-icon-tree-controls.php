<?php

$iconHelper = file_get_contents(dirname(__DIR__).'/src/helpers/BackendIcon.php');

if (strpos($iconHelper, "'folder-open' =>") === false) {
    fwrite(STDERR, "BackendIcon has no open-folder icon.\n");
    exit(1);
}

if (strpos($iconHelper, "'minus' =>") === false) {
    fwrite(STDERR, "BackendIcon has no collapse icon.\n");
    exit(1);
}

echo "backend-icon-tree-controls: OK\n";
