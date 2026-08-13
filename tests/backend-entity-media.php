<?php

$widget = file_get_contents(dirname(__DIR__).'/src/widgets/BackendEntityMedia.php');
$styles = file_get_contents(dirname(__DIR__).'/src/assets/src/backend.css');

function entityMediaExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

entityMediaExpect(strpos($widget, "BackendIcon::render(\$this->icon") !== false, 'Entity media does not use semantic backend icons.');
entityMediaExpect(strpos($widget, 'sx-collection-cell__media--accent') !== false, 'Entity fallback has no accent modifier.');
entityMediaExpect(strpos($widget, "'loading'") !== false, 'Entity images are not lazy-loaded.');
entityMediaExpect(strpos($styles, '.sx-collection-cell__media--accent') !== false, 'Entity accent media has no shared styles.');
entityMediaExpect(strpos($styles, 'var(--sx-color-accent-soft)') !== false, 'Entity media does not use backend theme tokens.');

echo "Backend entity media contract: OK\n";
