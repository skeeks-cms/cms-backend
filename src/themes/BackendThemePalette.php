<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\themes;

use yii\base\InvalidConfigException;

/**
 * Expands a small, validated theme-editor palette into the public CSS facade.
 *
 * Component tokens remain private to theme.css and consume the generated
 * --sx-color-* variables. This class intentionally does not accept arbitrary
 * CSS variable names or arbitrary CSS values.
 */
class BackendThemePalette
{
    const MODE_LIGHT = 'light';
    const MODE_DARK = 'dark';

    /** Stable storage schema for a user-editable palette. */
    const INPUT_KEYS = [
        'accent',
        'accentSecondary',
        'canvas',
        'surface',
        'surfaceMuted',
        'text',
        'textMuted',
        'border',
        'success',
        'warning',
        'danger',
    ];

    const DEFAULT_INPUT = [
        self::MODE_LIGHT => [
            'accent'       => '#ee4d7d',
            'accentSecondary' => '#efd740',
            'canvas'       => '#eef2f4',
            'surface'      => '#ffffff',
            'surfaceMuted' => '#f9f9f9',
            'text'         => '#262626',
            'textMuted'    => '#6f7484',
            'border'       => '#dedede',
            'success'      => '#23754a',
            'warning'      => '#95611d',
            'danger'       => '#bd4348',
        ],
        self::MODE_DARK => [
            'accent'       => '#f06f91',
            'accentSecondary' => '#efd740',
            'canvas'       => '#11151b',
            'surface'      => '#171c24',
            'surfaceMuted' => '#1d232c',
            'text'         => '#e7ebf2',
            'textMuted'    => '#9ea8b7',
            'border'       => '#2c343f',
            'success'      => '#6fce9b',
            'warning'      => '#e5b86c',
            'danger'       => '#f08b90',
        ],
    ];

    /**
     * Hand-tuned defaults preserve the current backend appearance exactly.
     * Generated variants are used only when their base inputs change.
     */
    const DEFAULT_OUTPUT = [
        self::MODE_LIGHT => [
            '--sx-color-accent'             => '#ee4d7d',
            '--sx-color-accent-secondary'   => '#efd740',
            '--sx-color-accent-hover'       => '#d64571',
            '--sx-color-accent-active'      => '#d64571',
            '--sx-color-accent-contrast'    => '#10141a',
            '--sx-color-accent-gradient-contrast' => '#10141a',
            '--sx-color-accent-soft'        => '#fdedf2',
            '--sx-color-accent-border'      => '#f8b4c8',
            '--sx-color-canvas'             => '#eef2f4',
            '--sx-color-canvas-translucent' => 'rgba(238, 242, 244, .58)',
            '--sx-color-surface'            => '#fff',
            '--sx-color-surface-raised'     => '#fff',
            '--sx-color-surface-muted'      => '#f9f9f9',
            '--sx-color-surface-hover'      => '#f4f6f8',
            '--sx-color-text'               => '#262626',
            '--sx-color-text-muted'         => '#6f7484',
            '--sx-color-text-subtle'        => '#707583',
            '--sx-color-border'             => '#dedede',
            '--sx-color-border-strong'      => '#c2c2c2',
            '--sx-color-focus-ring'         => 'rgba(238, 77, 125, .28)',
            '--sx-color-success'            => '#23754a',
            '--sx-color-success-hover'      => '#1b633e',
            '--sx-color-success-soft'       => '#eef9f3',
            '--sx-color-success-on-soft'    => '#23754a',
            '--sx-color-success-on-surface' => '#23754a',
            '--sx-color-success-contrast'   => '#fff',
            '--sx-color-warning'            => '#95611d',
            '--sx-color-warning-hover'      => '#7d4f16',
            '--sx-color-warning-soft'       => '#fff7e9',
            '--sx-color-warning-on-soft'    => '#95611d',
            '--sx-color-warning-on-surface' => '#95611d',
            '--sx-color-warning-contrast'   => '#fff',
            '--sx-color-danger'             => '#bd4348',
            '--sx-color-danger-hover'       => '#a7383d',
            '--sx-color-danger-soft'        => '#fff3f3',
            '--sx-color-danger-on-soft'     => '#bd4348',
            '--sx-color-danger-on-surface'  => '#bd4348',
            '--sx-color-danger-contrast'    => '#fff',
            '--sx-color-info'               => 'var(--sx-color-accent)',
            '--sx-color-info-soft'          => 'var(--sx-color-accent-soft)',
        ],
        self::MODE_DARK => [
            '--sx-color-accent'             => '#f06f91',
            '--sx-color-accent-secondary'   => '#efd740',
            '--sx-color-accent-hover'       => '#f2809e',
            '--sx-color-accent-active'      => '#f2809e',
            '--sx-color-accent-contrast'    => '#10141a',
            '--sx-color-accent-gradient-contrast' => '#10141a',
            '--sx-color-accent-soft'        => '#422d3a',
            '--sx-color-accent-border'      => '#7f4458',
            '--sx-color-canvas'             => '#11151b',
            '--sx-color-canvas-translucent' => 'rgba(17, 21, 27, .78)',
            '--sx-color-surface'            => '#171c24',
            '--sx-color-surface-raised'     => '#1b2029',
            '--sx-color-surface-muted'      => '#1d232c',
            '--sx-color-surface-hover'      => '#222a35',
            '--sx-color-text'               => '#e7ebf2',
            '--sx-color-text-muted'         => '#9ea8b7',
            '--sx-color-text-subtle'        => '#7f8998',
            '--sx-color-border'             => '#2c343f',
            '--sx-color-border-strong'      => '#414b59',
            '--sx-color-focus-ring'         => 'rgba(240, 111, 145, .34)',
            '--sx-color-success'            => '#6fce9b',
            '--sx-color-success-hover'      => '#83d8a9',
            '--sx-color-success-soft'       => '#1d3a2c',
            '--sx-color-success-on-soft'    => '#6fce9b',
            '--sx-color-success-on-surface' => '#6fce9b',
            '--sx-color-success-contrast'   => '#102119',
            '--sx-color-warning'            => '#e5b86c',
            '--sx-color-warning-hover'      => '#edc985',
            '--sx-color-warning-soft'       => '#3d3120',
            '--sx-color-warning-on-soft'    => '#e5b86c',
            '--sx-color-warning-on-surface' => '#e5b86c',
            '--sx-color-warning-contrast'   => '#2b1d08',
            '--sx-color-danger'             => '#f08b90',
            '--sx-color-danger-hover'       => '#f2a0a4',
            '--sx-color-danger-soft'        => '#47282b',
            '--sx-color-danger-on-soft'     => '#f08b90',
            '--sx-color-danger-on-surface'  => '#f08b90',
            '--sx-color-danger-contrast'    => '#241214',
            '--sx-color-info'               => 'var(--sx-color-accent)',
            '--sx-color-info-soft'          => 'var(--sx-color-accent-soft)',
        ],
    ];

    /** @var array */
    private $_input = [];

    /**
     * @param array $input Partial light/dark palette configuration.
     * @throws InvalidConfigException
     */
    public function __construct(array $input = [])
    {
        $this->_input = $this->normalizeInput($input);
    }

    /** @return array */
    public function getInput()
    {
        return $this->_input;
    }

    /**
     * @param string $mode
     * @return array CSS variable name => safe CSS value.
     * @throws InvalidConfigException
     */
    public function toCssVariables($mode)
    {
        if (!isset(self::DEFAULT_INPUT[$mode])) {
            throw new InvalidConfigException("Unknown backend theme palette mode: {$mode}");
        }

        $input = array_merge(self::DEFAULT_INPUT[$mode], $this->_input[$mode]);
        $output = self::DEFAULT_OUTPUT[$mode];
        $isDark = $mode === self::MODE_DARK;

        $this->expandAccent($output, $input, $mode, $isDark);
        $this->expandCanvas($output, $input, $mode, $isDark);
        $this->expandSurfaces($output, $input, $mode, $isDark);
        $this->expandText($output, $input, $mode, $isDark);
        $this->expandBorder($output, $input, $mode);
        $this->expandStatus($output, $input, $mode, 'success', $isDark);
        $this->expandStatus($output, $input, $mode, 'warning', $isDark);
        $this->expandStatus($output, $input, $mode, 'danger', $isDark);

        return $output;
    }

    /**
     * Returns fixed selectors only; callers cannot inject selectors or rules.
     *
     * @return string
     */
    public function toCss()
    {
        return $this->renderRule(
            ':root, html[data-sx-theme="light"]',
            $this->toCssVariables(self::MODE_LIGHT)
        )
            . "\n\n"
            . $this->renderRule('html[data-sx-theme="dark"]', $this->toCssVariables(self::MODE_DARK));
    }

    /**
     * @param array $input
     * @return array
     * @throws InvalidConfigException
     */
    private function normalizeInput(array $input)
    {
        $normalized = [
            self::MODE_LIGHT => [],
            self::MODE_DARK  => [],
        ];

        foreach ($input as $mode => $values) {
            if (!array_key_exists($mode, $normalized)) {
                throw new InvalidConfigException("Unknown backend theme palette mode: {$mode}");
            }
            if (!is_array($values)) {
                throw new InvalidConfigException("Backend theme palette mode {$mode} must be an array.");
            }

            foreach ($values as $key => $value) {
                if (!in_array($key, self::INPUT_KEYS, true)) {
                    throw new InvalidConfigException("Unknown backend theme palette key: {$key}");
                }
                $normalized[$mode][$key] = $this->normalizeHex($value, $mode, $key);
            }
        }

        return $normalized;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws InvalidConfigException
     */
    private function normalizeHex($value, $mode, $key)
    {
        if (!is_string($value) || !preg_match('/^#[0-9a-f]{3}([0-9a-f]{3})?$/i', $value)) {
            throw new InvalidConfigException(
                "Backend theme palette value {$mode}.{$key} must be a #rgb or #rrggbb color."
            );
        }

        $value = strtolower($value);
        if (strlen($value) === 4) {
            $value = '#' . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];
        }

        return $value;
    }

    private function expandAccent(array &$output, array $input, $mode, $isDark)
    {
        $accentChanged = $input['accent'] !== self::DEFAULT_INPUT[$mode]['accent'];
        $accentWasConfigured = array_key_exists('accent', $this->_input[$mode]);
        $secondaryWasConfigured = array_key_exists('accentSecondary', $this->_input[$mode]);
        $accentSecondary = $secondaryWasConfigured
            ? $input['accentSecondary']
            : ($accentWasConfigured
                ? $this->adjust($input['accent'], $isDark ? .12 : -.10)
                : self::DEFAULT_INPUT[$mode]['accentSecondary']);
        $secondaryChanged = $accentSecondary !== self::DEFAULT_INPUT[$mode]['accentSecondary'];
        $surfaceChanged = $input['surface'] !== self::DEFAULT_INPUT[$mode]['surface'];
        if (!$accentChanged && !$secondaryChanged && !$surfaceChanged) {
            return;
        }

        $accent = $input['accent'];
        if ($secondaryChanged) {
            $output['--sx-color-accent-secondary'] = $accentSecondary;
        }
        if ($accentChanged || $secondaryChanged) {
            $output['--sx-color-accent-gradient-contrast'] = $this->gradientContrast(
                $accent,
                $accentSecondary
            );
        }
        if ($accentChanged) {
            $output['--sx-color-accent'] = $accent;
            $output['--sx-color-accent-hover'] = $this->adjust($accent, $isDark ? .12 : -.10);
            $output['--sx-color-accent-active'] = $output['--sx-color-accent-hover'];
            $output['--sx-color-accent-contrast'] = $this->contrast($accent);
            $output['--sx-color-focus-ring'] = $this->rgba($accent, $isDark ? '.34' : '.28');
        }
        $output['--sx-color-accent-soft'] = $this->mix($accent, $input['surface'], $isDark ? .20 : .10);
        $output['--sx-color-accent-border'] = $this->mix($accent, $input['surface'], $isDark ? .48 : .42);
    }

    private function expandCanvas(array &$output, array $input, $mode, $isDark)
    {
        if ($input['canvas'] === self::DEFAULT_INPUT[$mode]['canvas']) {
            return;
        }

        $output['--sx-color-canvas'] = $input['canvas'];
        $output['--sx-color-canvas-translucent'] = $this->rgba($input['canvas'], $isDark ? '.78' : '.58');
    }

    private function expandSurfaces(array &$output, array $input, $mode, $isDark)
    {
        $surfaceChanged = $input['surface'] !== self::DEFAULT_INPUT[$mode]['surface'];
        $textChanged = $input['text'] !== self::DEFAULT_INPUT[$mode]['text'];

        if ($surfaceChanged) {
            $output['--sx-color-surface'] = $input['surface'];
            $output['--sx-color-surface-raised'] = $isDark
                ? $this->adjust($input['surface'], .025)
                : $input['surface'];
        }
        if ($input['surfaceMuted'] !== self::DEFAULT_INPUT[$mode]['surfaceMuted']) {
            $output['--sx-color-surface-muted'] = $input['surfaceMuted'];
        }
        if ($surfaceChanged || $textChanged) {
            $output['--sx-color-surface-hover'] = $this->mix(
                $input['text'],
                $input['surface'],
                $isDark ? .08 : .04
            );
        }
    }

    private function expandText(array &$output, array $input, $mode, $isDark)
    {
        if ($input['text'] !== self::DEFAULT_INPUT[$mode]['text']) {
            $output['--sx-color-text'] = $input['text'];
        }
        if ($input['textMuted'] !== self::DEFAULT_INPUT[$mode]['textMuted']
            || $input['surface'] !== self::DEFAULT_INPUT[$mode]['surface']) {
            $output['--sx-color-text-muted'] = $input['textMuted'];
            $output['--sx-color-text-subtle'] = $this->mix(
                $input['textMuted'],
                $input['surface'],
                $isDark ? .78 : .98
            );
        }
    }

    private function expandBorder(array &$output, array $input, $mode)
    {
        if ($input['border'] === self::DEFAULT_INPUT[$mode]['border']
            && $input['text'] === self::DEFAULT_INPUT[$mode]['text']) {
            return;
        }

        $output['--sx-color-border'] = $input['border'];
        $output['--sx-color-border-strong'] = $this->mix($input['text'], $input['border'], .18);
    }

    private function expandStatus(array &$output, array $input, $mode, $key, $isDark)
    {
        $colorChanged = $input[$key] !== self::DEFAULT_INPUT[$mode][$key];
        $surfaceChanged = $input['surface'] !== self::DEFAULT_INPUT[$mode]['surface'];
        if (!$colorChanged && !$surfaceChanged) {
            return;
        }

        $color = $input[$key];
        $prefix = '--sx-color-' . $key;
        if ($colorChanged) {
            $output[$prefix] = $color;
            $output[$prefix . '-hover'] = $this->adjust($color, $isDark ? .10 : -.10);
            $output[$prefix . '-contrast'] = $this->contrast($color);
        }
        $soft = $this->mix($color, $input['surface'], $isDark ? .18 : .08);
        $output[$prefix . '-soft'] = $soft;
        $output[$prefix . '-on-soft'] = $this->accessibleForeground($color, $soft);
        $output[$prefix . '-on-surface'] = $this->accessibleForeground($color, $input['surface']);
    }

    private function renderRule($selector, array $variables)
    {
        $lines = [];
        foreach ($variables as $name => $value) {
            $lines[] = "    {$name}: {$value};";
        }

        return $selector . " {\n" . implode("\n", $lines) . "\n}";
    }

    private function adjust($hex, $amount)
    {
        $rgb = $this->rgb($hex);
        foreach ($rgb as &$channel) {
            $target = $amount < 0 ? 0 : 255;
            $channel = (int) round($channel + ($target - $channel) * abs($amount));
        }
        unset($channel);

        return $this->hex($rgb);
    }

    private function mix($foreground, $background, $foregroundWeight)
    {
        $foregroundRgb = $this->rgb($foreground);
        $backgroundRgb = $this->rgb($background);
        $mixed = [];
        foreach ([0, 1, 2] as $index) {
            $mixed[$index] = (int) round(
                $foregroundRgb[$index] * $foregroundWeight
                + $backgroundRgb[$index] * (1 - $foregroundWeight)
            );
        }

        return $this->hex($mixed);
    }

    private function contrast($hex)
    {
        $rgb = $this->rgb($hex);
        $luminance = (0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2]) / 255;

        return $luminance > .58 ? '#10141a' : '#ffffff';
    }

    /**
     * Keeps the selected semantic color when it is readable and otherwise
     * moves it only as far as necessary toward black or white.
     */
    private function accessibleForeground($foreground, $background)
    {
        if ($this->contrastRatio($foreground, $background) >= 4.5) {
            return $foreground;
        }

        $dark = '#000000';
        $light = '#ffffff';
        $target = $this->contrastRatio($dark, $background) > $this->contrastRatio($light, $background)
            ? $dark
            : $light;

        for ($percent = 1; $percent <= 100; $percent++) {
            $candidate = $this->mix($target, $foreground, $percent / 100);
            if ($this->contrastRatio($candidate, $background) >= 4.5) {
                return $candidate;
            }
        }

        return $target;
    }

    private function contrastRatio($first, $second)
    {
        $firstLuminance = $this->relativeLuminance($first);
        $secondLuminance = $this->relativeLuminance($second);

        return (max($firstLuminance, $secondLuminance) + .05)
            / (min($firstLuminance, $secondLuminance) + .05);
    }

    private function gradientContrast($first, $second)
    {
        $dark = '#10141a';
        $light = '#ffffff';
        $darkScore = min(
            $this->contrastRatio($dark, $first),
            $this->contrastRatio($dark, $second)
        );
        $lightScore = min(
            $this->contrastRatio($light, $first),
            $this->contrastRatio($light, $second)
        );

        return $darkScore >= $lightScore ? $dark : $light;
    }

    private function relativeLuminance($hex)
    {
        $channels = $this->rgb($hex);
        foreach ($channels as &$channel) {
            $channel /= 255;
            $channel = $channel <= .04045
                ? $channel / 12.92
                : pow(($channel + .055) / 1.055, 2.4);
        }
        unset($channel);

        return .2126 * $channels[0] + .7152 * $channels[1] + .0722 * $channels[2];
    }

    private function rgba($hex, $alpha)
    {
        $rgb = $this->rgb($hex);
        return "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, {$alpha})";
    }

    private function rgb($hex)
    {
        return [
            hexdec(substr($hex, 1, 2)),
            hexdec(substr($hex, 3, 2)),
            hexdec(substr($hex, 5, 2)),
        ];
    }

    private function hex(array $rgb)
    {
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }
}
