<?php

$autoloadCandidates = [
    '/app/vendor/autoload.php',
    dirname(__DIR__).'/vendor/autoload.php',
    dirname(__DIR__, 3).'/autoload.php',
];

foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
        break;
    }
}

use skeeks\cms\backend\widgets\BackendShellProfileWidget;

function headerExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$css = file_get_contents(dirname(__DIR__).'/src/assets/src/shell-header.css');
$shellCss = file_get_contents(dirname(__DIR__).'/src/assets/src/shell.css');
$theme = file_get_contents(dirname(__DIR__).'/src/assets/src/theme.css');
$profileWidget = file_get_contents(dirname(__DIR__).'/src/widgets/BackendShellProfileWidget.php');
$headerView = file_get_contents(dirname(__DIR__).'/src/widgets/views/backend-shell-header.php');

headerExpect(class_exists(BackendShellProfileWidget::class), 'Shared shell profile widget is not autoloadable.');
headerExpect(strpos($profileWidget, "'sx-shell-profile__toggle'") !== false, 'Profile widget does not emit the semantic toggle.');
headerExpect(strpos($profileWidget, "BackendIcon::render('chevron-down'") !== false, 'Profile widget does not use the semantic chevron icon.');
headerExpect(preg_match_all('/^\\.sx-shell-profile__toggle \\{/m', $css) === 1, 'Profile toggle CSS must have one canonical definition.');
headerExpect(substr_count($theme, '--sx-shell-header-profile-avatar-size:') === 1, 'Profile avatar token must have one canonical default.');
headerExpect(strpos($theme, '--sx-shell-header-action-radius: 8px;') !== false, 'Header action radius token is missing.');
headerExpect(strpos($css, '.sx-shell-profile__toggle[aria-expanded="true"]') !== false, 'Open profile state is not styled.');
headerExpect(strpos($css, '.sx-shell-profile__chevron.fas') !== false, 'Legacy Font Awesome profile chevron bridge is missing.');
headerExpect(strpos($css, '.sx-btn-backend-header a:not(.sx-shell-header__action)') !== false, 'Legacy header wrapper still overrides semantic actions.');
headerExpect(strpos($shellCss, '.sx-btn-backend-header a:not(.sx-shell-header__action):hover') !== false, 'Legacy hover adapter still overrides semantic actions.');
headerExpect(strpos($theme, '--sx-shell-header-context-gap: 44px;') !== false, 'Header context gap token is missing.');
headerExpect(strpos($css, 'margin-left: var(--sx-shell-header-context-gap, 44px);') !== false, 'Header context does not consume the shared gap token.');
headerExpect(strpos($headerView, "if (trim(\$context) !== '')") !== false, 'Empty header context slots are still rendered.');

echo "Backend shell header contract: OK\n";
