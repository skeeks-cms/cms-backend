<?php

$autoloadCandidates = [
    '/app/vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(__DIR__, 3) . '/autoload.php',
];

foreach ($autoloadCandidates as $autoload) {
    if (is_file($autoload)) {
        require $autoload;
        break;
    }
}

$yiiCandidates = [
    '/app/vendor/yiisoft/yii2/Yii.php',
    dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php',
    dirname(__DIR__, 3) . '/yiisoft/yii2/Yii.php',
];
foreach ($yiiCandidates as $yiiBootstrap) {
    if (is_file($yiiBootstrap)) {
        require_once $yiiBootstrap;
        break;
    }
}

if (!class_exists(\skeeks\cms\backend\themes\BackendThemePalette::class)) {
    throw new RuntimeException('Composer autoload for cms-backend was not found.');
}

use skeeks\cms\backend\themes\BackendTheme;
use skeeks\cms\backend\themes\BackendThemePalette;
use yii\base\InvalidConfigException;

function expectSame($expected, $actual, $message)
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true)
            . "\nActual: " . var_export($actual, true));
    }
}

function expectInvalid(array $input, $message)
{
    try {
        new BackendThemePalette($input);
    } catch (InvalidConfigException $exception) {
        return;
    }

    throw new RuntimeException($message);
}

function relativeLuminance($hex)
{
    $channels = [
        hexdec(substr($hex, 1, 2)),
        hexdec(substr($hex, 3, 2)),
        hexdec(substr($hex, 5, 2)),
    ];
    foreach ($channels as &$channel) {
        $channel /= 255;
        $channel = $channel <= .04045
            ? $channel / 12.92
            : pow(($channel + .055) / 1.055, 2.4);
    }
    unset($channel);

    return .2126 * $channels[0] + .7152 * $channels[1] + .0722 * $channels[2];
}

function contrastRatio($first, $second)
{
    $firstLuminance = relativeLuminance($first);
    $secondLuminance = relativeLuminance($second);

    return (max($firstLuminance, $secondLuminance) + .05)
        / (min($firstLuminance, $secondLuminance) + .05);
}

function readCssVariables($block)
{
    preg_match_all('/(--sx-color-[a-z0-9-]+):\s*([^;]+);/i', $block, $matches, PREG_SET_ORDER);
    $variables = [];
    foreach ($matches as $match) {
        $variables[$match[1]] = trim($match[2]);
    }

    return $variables;
}

$themeCss = file_get_contents(dirname(__DIR__) . '/src/assets/src/theme.css');
preg_match('/:root\s*\{(.*?)\n\}/s', $themeCss, $lightBlock);
preg_match('/html\[data-sx-theme="dark"\]\s*\{(.*?)\n\}/s', $themeCss, $darkBlock);
$cssLight = readCssVariables($lightBlock[1]);
$cssDark = array_merge($cssLight, readCssVariables($darkBlock[1]));

$defaults = new BackendThemePalette();
foreach ($defaults->toCssVariables(BackendThemePalette::MODE_LIGHT) as $name => $value) {
    expectSame($cssLight[$name], $value, "The generated light default {$name} must match theme.css.");
}
foreach ($defaults->toCssVariables(BackendThemePalette::MODE_DARK) as $name => $value) {
    expectSame($cssDark[$name], $value, "The generated dark default {$name} must match theme.css.");
}

$emptyTheme = new BackendTheme();
expectSame('', $emptyTheme->paletteCss, 'An empty BackendTheme palette must not register CSS.');
expectSame(
    ['data-sx-header-light' => 'dark', 'data-sx-header-dark' => 'dark'],
    $emptyTheme->headerAppearanceAttributes,
    'The shared header must default to a dark surface in both page themes.'
);

$customStatusPalette = new BackendThemePalette([
    'dark' => [
        'surface' => '#1e1e20',
        'danger' => '#a6595d',
    ],
]);
$customStatusDark = $customStatusPalette->toCssVariables('dark');
expectSame(
    '#a6595d',
    $customStatusDark['--sx-color-danger'],
    'The user-selected semantic color must remain unchanged.'
);
if (contrastRatio(
    $customStatusDark['--sx-color-danger-on-soft'],
    $customStatusDark['--sx-color-danger-soft']
) < 4.5) {
    throw new RuntimeException('Generated status text on a soft background must meet WCAG AA contrast.');
}
if (contrastRatio(
    $customStatusDark['--sx-color-danger-on-surface'],
    '#1e1e20'
) < 4.5) {
    throw new RuntimeException('Generated semantic text on a surface must meet WCAG AA contrast.');
}

$configuredHeaderTheme = new BackendTheme([
    'headerModes' => [
        BackendTheme::THEME_MODE_LIGHT => BackendTheme::HEADER_MODE_THEME,
        BackendTheme::THEME_MODE_DARK  => BackendTheme::HEADER_MODE_LIGHT,
    ],
]);
expectSame(
    ['data-sx-header-light' => 'light', 'data-sx-header-dark' => 'light'],
    $configuredHeaderTheme->headerAppearanceAttributes,
    'Header appearance must be configurable independently for each page theme.'
);

$palette = new BackendThemePalette([
    'light' => [
        'accent' => '#06c',
    ],
]);
$light = $palette->toCssVariables('light');
$dark = $palette->toCssVariables('dark');

expectSame('#0066cc', $light['--sx-color-accent'], 'Short hex colors must be normalized.');
expectSame('#005cb8', $light['--sx-color-accent-hover'], 'Accent hover must be generated.');
expectSame(
    '#005cb8',
    $light['--sx-color-accent-secondary'],
    'A stored one-color palette must use the generated hover color as its compatible second stop.'
);
expectSame('#ffffff', $light['--sx-color-accent-contrast'], 'Readable accent contrast must be generated.');
expectSame(
    '#ffffff',
    $light['--sx-color-accent-gradient-contrast'],
    'Gradient contrast must be calculated against both stops.'
);
expectSame(
    BackendThemePalette::DEFAULT_OUTPUT['dark'],
    $dark,
    'Changing the light palette must not alter dark defaults.'
);

$twoAccentPalette = new BackendThemePalette([
    'light' => [
        'accent' => '#28d745',
        'accentSecondary' => '#efd740',
    ],
]);
$twoAccentLight = $twoAccentPalette->toCssVariables('light');
expectSame('#28d745', $twoAccentLight['--sx-color-accent'], 'The primary accent must remain editable.');
expectSame(
    '#efd740',
    $twoAccentLight['--sx-color-accent-secondary'],
    'The secondary accent must remain an independent editable gradient stop.'
);
expectSame(
    '#10141a',
    $twoAccentLight['--sx-color-accent-gradient-contrast'],
    'A light two-color gradient must select dark readable text.'
);

$surfacePalette = new BackendThemePalette([
    'light' => [
        'surface' => '#fafafa',
    ],
]);
$surfaceLight = $surfacePalette->toCssVariables('light');
expectSame(
    BackendThemePalette::DEFAULT_OUTPUT['light']['--sx-color-accent-hover'],
    $surfaceLight['--sx-color-accent-hover'],
    'Changing a surface must not regenerate an unchanged accent state.'
);
expectSame(
    BackendThemePalette::DEFAULT_OUTPUT['light']['--sx-color-success-hover'],
    $surfaceLight['--sx-color-success-hover'],
    'Changing a surface must not regenerate an unchanged status state.'
);
if ($surfaceLight['--sx-color-text-subtle'] === BackendThemePalette::DEFAULT_OUTPUT['light']['--sx-color-text-subtle']) {
    throw new RuntimeException('Changing a surface must regenerate dependent subtle text.');
}

$previewRuntime = file_get_contents(dirname(__DIR__).'/src/assets/src/theme-customizer.js');
$customizerView = file_get_contents(dirname(__DIR__).'/src/widgets/views/theme-customizer-panel.php');
foreach (BackendThemePalette::INPUT_KEYS as $inputKey) {
    if (strpos($previewRuntime, $inputKey.": '--sx-color-") === false) {
        throw new RuntimeException("Live preview input map is missing {$inputKey}.");
    }
    if (strpos($customizerView, "'{$inputKey}'") === false) {
        throw new RuntimeException("Theme customizer form is missing {$inputKey}.");
    }
}
if (strpos($customizerView, "'--sx-") !== false) {
    throw new RuntimeException('Theme customizer must not expose internal CSS variable names.');
}
foreach (['success', 'warning', 'danger'] as $status) {
    foreach (['on-soft', 'on-surface'] as $variant) {
        $name = "--sx-color-{$status}-{$variant}";
        if (strpos($previewRuntime, "'{$name}'") === false) {
            throw new RuntimeException("Live preview output is missing {$name}.");
        }
    }
}
if (strpos($previewRuntime, "accessibleForeground(draft[key], soft)") === false
    || strpos($previewRuntime, "accessibleForeground(draft[key], draft.surface)") === false) {
    throw new RuntimeException('Live preview must regenerate accessible status foregrounds.');
}
if (strpos($previewRuntime, 'gradientContrast(') === false) {
    throw new RuntimeException('Live preview must regenerate contrast for both accent gradient stops.');
}
if (strpos($previewRuntime, 'body.scrollTop = 0;') === false) {
    throw new RuntimeException('Theme customizer must reopen at the beginning of its field list.');
}
if (strpos($customizerView, 'data-sx-theme-customizer-reset-default') === false
    || strpos($previewRuntime, 'config.resetDefaultUrl') === false
    || strpos($previewRuntime, 'config.resetDefaultConfirm') === false) {
    throw new RuntimeException('Theme customizer must expose the protected shared-palette reset action.');
}

$css = $palette->toCss();
expectSame(
    1,
    substr_count($css, ':root, html[data-sx-theme="light"] {'),
    'The palette must emit one fixed light selector with enough specificity for project themes.'
);
expectSame(
    1,
    substr_count($css, 'html[data-sx-theme="dark"] {'),
    'The palette must emit one fixed dark selector.'
);

$configuredTheme = new BackendTheme([
    'palette' => [
        'light' => ['accent' => '#06c'],
    ],
]);
expectSame(
    $css,
    $configuredTheme->paletteCss,
    'BackendTheme must expose the validated expanded palette to the shared layout.'
);

expectInvalid(
    ['light' => ['--sx-button-radius' => '#fff']],
    'Arbitrary CSS variable names must be rejected.'
);
expectInvalid(
    ['light' => ['accent' => '#fff; color: red']],
    'CSS injection in palette values must be rejected.'
);
expectInvalid(
    ['contrast' => ['accent' => '#fff']],
    'Unknown palette modes must be rejected.'
);

echo "BackendThemePalette tests passed.\n";
