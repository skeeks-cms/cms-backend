<?php

function buttonThemeExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$theme = file_get_contents(dirname(__DIR__).'/src/assets/src/theme.css');
$shell = file_get_contents(dirname(__DIR__).'/src/assets/src/shell.css');

buttonThemeExpect(strpos($theme, '--sx-color-accent-gradient: linear-gradient(135deg, var(--sx-color-accent) 0%, var(--sx-color-accent-secondary, var(--sx-color-accent-hover)) 100%);') !== false, 'Primary gradient must derive from both editable accent colors.');
buttonThemeExpect(strpos($theme, '--sx-button-primary-color: var(--sx-color-accent-gradient-contrast, var(--sx-color-accent-contrast));') !== false, 'Primary text must use contrast calculated for both gradient stops.');
buttonThemeExpect(strpos($theme, '--sx-button-height-md: 42px;') !== false, 'Default button height must be 42px.');
buttonThemeExpect(strpos($theme, '--sx-button-radius: 8px;') !== false, 'Default button radius must be 8px.');
buttonThemeExpect(strpos($theme, '--sx-button-font-weight: 600;') !== false, 'Default button weight must be 600.');
buttonThemeExpect(strpos($theme, '--sx-button-primary-border-width: 0;') !== false, 'Primary buttons must not reserve a border.');
buttonThemeExpect(strpos($theme, '--sx-button-primary-shadow: 0 5px 14px color-mix(in srgb, var(--sx-color-accent) 16%, transparent);') !== false, 'Primary shadow must derive from the editable accent palette.');
buttonThemeExpect(strpos($theme, '--sx-button-primary-hover-filter: brightness(.95);') !== false, 'Primary hover filter is missing.');
buttonThemeExpect(strpos($theme, '--sx-button-secondary-background: var(--sx-color-surface);') !== false, 'Secondary button surface is missing.');
buttonThemeExpect(preg_match('/^\.btn\s*\{/m', $shell) === 0, 'Legacy shell button radius still overrides the theme contract.');
buttonThemeExpect(strpos($shell, '.btn:visited,') === false, 'Legacy shell button state still suppresses semantic shadows.');
buttonThemeExpect(strpos($shell, '.btn-primary {') === false, 'Legacy shell primary color still overrides the theme contract.');

echo "Backend button theme contract: OK\n";
