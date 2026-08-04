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
expectSame('#ffffff', $light['--sx-color-accent-contrast'], 'Readable accent contrast must be generated.');
expectSame(
    BackendThemePalette::DEFAULT_OUTPUT['dark'],
    $dark,
    'Changing the light palette must not alter dark defaults.'
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
