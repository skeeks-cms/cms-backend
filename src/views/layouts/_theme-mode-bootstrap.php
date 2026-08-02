<?php

use yii\helpers\Json;

/* @var $theme yii\base\Theme */

$themeMode = isset($theme->normalizedThemeMode) ? $theme->normalizedThemeMode : 'light';
$themeModeStorageKey = isset($theme->themeModeStorageKey)
    ? (string) $theme->themeModeStorageKey
    : 'sx-theme-mode';
$allowClientThemeMode = !isset($theme->allowClientThemeMode) || (bool) $theme->allowClientThemeMode;
$configuredThemeMode = isset($theme->themeMode) ? $theme->themeMode : null;
$colorScheme = isset($theme->color_scheme) ? $theme->color_scheme : 'light';
$useSystemThemePreference = $configuredThemeMode === null && $colorScheme !== 'dark';
?>
<script>
    (function (root, defaultMode, storageKey, allowStoredMode, useSystemPreference) {
        var allowedModes = ['light', 'dark'];
        var mode = allowedModes.indexOf(defaultMode) === -1 ? 'light' : defaultMode;
        var source = 'config';
        var hasStoredMode = false;

        if (allowStoredMode) {
            try {
                var storedMode = window.localStorage.getItem(storageKey);
                if (allowedModes.indexOf(storedMode) !== -1) {
                    mode = storedMode;
                    source = 'user';
                    hasStoredMode = true;
                }
            } catch (e) {
            }
        }

        if (!hasStoredMode && useSystemPreference && window.matchMedia) {
            mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            source = 'system';
        }

        root.setAttribute('data-sx-theme-mode', mode);
        root.setAttribute('data-sx-theme', mode);
        root.setAttribute('data-sx-theme-source', source);
        root.style.colorScheme = mode;
    })(
        document.documentElement,
        <?= Json::htmlEncode($themeMode) ?>,
        <?= Json::htmlEncode($themeModeStorageKey) ?>,
        <?= $allowClientThemeMode ? 'true' : 'false' ?>,
        <?= $useSystemThemePreference ? 'true' : 'false' ?>
    );
</script>
