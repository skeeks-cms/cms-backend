(function (window, document) {
    'use strict';

    var root = document.documentElement;
    var previewStyleId = 'sx-theme-customizer-preview';
    var baseVariables = {
        accent: '--sx-color-accent',
        canvas: '--sx-color-canvas',
        surface: '--sx-color-surface',
        surfaceMuted: '--sx-color-surface-muted',
        text: '--sx-color-text',
        textMuted: '--sx-color-text-muted',
        border: '--sx-color-border',
        success: '--sx-color-success',
        warning: '--sx-color-warning',
        danger: '--sx-color-danger'
    };
    var outputNames = [
        '--sx-color-accent',
        '--sx-color-accent-hover',
        '--sx-color-accent-active',
        '--sx-color-accent-contrast',
        '--sx-color-accent-soft',
        '--sx-color-accent-border',
        '--sx-color-canvas',
        '--sx-color-canvas-translucent',
        '--sx-color-surface',
        '--sx-color-surface-raised',
        '--sx-color-surface-muted',
        '--sx-color-surface-hover',
        '--sx-color-text',
        '--sx-color-text-muted',
        '--sx-color-text-subtle',
        '--sx-color-border',
        '--sx-color-border-strong',
        '--sx-color-focus-ring',
        '--sx-color-success',
        '--sx-color-success-hover',
        '--sx-color-success-soft',
        '--sx-color-success-contrast',
        '--sx-color-warning',
        '--sx-color-warning-hover',
        '--sx-color-warning-soft',
        '--sx-color-warning-contrast',
        '--sx-color-danger',
        '--sx-color-danger-hover',
        '--sx-color-danger-soft',
        '--sx-color-danger-contrast',
        '--sx-color-info',
        '--sx-color-info-soft'
    ];

    var clone = function (value) {
        return JSON.parse(JSON.stringify(value));
    };

    var normalizeHex = function (value) {
        value = String(value || '').trim().toLowerCase();
        if (/^#[0-9a-f]{3}$/.test(value)) {
            return '#' + value[1] + value[1] + value[2] + value[2] + value[3] + value[3];
        }
        if (/^#[0-9a-f]{6}$/.test(value)) {
            return value;
        }
        return null;
    };

    var rgb = function (hex) {
        return [
            parseInt(hex.slice(1, 3), 16),
            parseInt(hex.slice(3, 5), 16),
            parseInt(hex.slice(5, 7), 16)
        ];
    };

    var hex = function (channels) {
        return '#' + channels.map(function (channel) {
            return Math.max(0, Math.min(255, channel)).toString(16).padStart(2, '0');
        }).join('');
    };

    var adjust = function (color, amount) {
        var channels = rgb(color);
        var target = amount < 0 ? 0 : 255;
        return hex(channels.map(function (channel) {
            return Math.round(channel + (target - channel) * Math.abs(amount));
        }));
    };

    var mix = function (foreground, background, foregroundWeight) {
        var foregroundRgb = rgb(foreground);
        var backgroundRgb = rgb(background);
        return hex([0, 1, 2].map(function (index) {
            return Math.round(
                foregroundRgb[index] * foregroundWeight
                + backgroundRgb[index] * (1 - foregroundWeight)
            );
        }));
    };

    var contrast = function (color) {
        var channels = rgb(color);
        var luminance = (
            .2126 * channels[0]
            + .7152 * channels[1]
            + .0722 * channels[2]
        ) / 255;
        return luminance > .58 ? '#10141a' : '#ffffff';
    };

    var rgba = function (color, alpha) {
        var channels = rgb(color);
        return 'rgba(' + channels.join(', ') + ', ' + alpha + ')';
    };

    var expand = function (snapshot, draft, mode) {
        var initial = snapshot.base;
        var output = clone(snapshot.output);
        var isDark = mode === 'dark';
        var accentChanged = draft.accent !== initial.accent;
        var surfaceChanged = draft.surface !== initial.surface;
        var textChanged = draft.text !== initial.text;

        Object.keys(baseVariables).forEach(function (key) {
            output[baseVariables[key]] = draft[key];
        });

        if (accentChanged) {
            output['--sx-color-accent-hover'] = adjust(draft.accent, isDark ? .12 : -.10);
            output['--sx-color-accent-active'] = output['--sx-color-accent-hover'];
            output['--sx-color-accent-contrast'] = contrast(draft.accent);
            output['--sx-color-focus-ring'] = rgba(draft.accent, isDark ? '.34' : '.28');
        }
        if (accentChanged || surfaceChanged) {
            output['--sx-color-accent-soft'] = mix(draft.accent, draft.surface, isDark ? .20 : .10);
            output['--sx-color-accent-border'] = mix(draft.accent, draft.surface, isDark ? .48 : .42);
        }
        if (draft.canvas !== initial.canvas) {
            output['--sx-color-canvas-translucent'] = rgba(draft.canvas, isDark ? '.78' : '.58');
        }
        if (surfaceChanged) {
            output['--sx-color-surface-raised'] = isDark ? adjust(draft.surface, .025) : draft.surface;
        }
        if (surfaceChanged || textChanged) {
            output['--sx-color-surface-hover'] = mix(draft.text, draft.surface, isDark ? .08 : .04);
        }
        if (draft.textMuted !== initial.textMuted) {
            output['--sx-color-text-subtle'] = mix(
                draft.textMuted,
                draft.surface,
                isDark ? .78 : .98
            );
        }
        if (draft.border !== initial.border || textChanged) {
            output['--sx-color-border-strong'] = mix(draft.text, draft.border, .18);
        }

        ['success', 'warning', 'danger'].forEach(function (key) {
            var colorChanged = draft[key] !== initial[key];
            var prefix = '--sx-color-' + key;
            if (colorChanged) {
                output[prefix + '-hover'] = adjust(draft[key], isDark ? .10 : -.10);
                output[prefix + '-contrast'] = contrast(draft[key]);
            }
            if (colorChanged || surfaceChanged) {
                output[prefix + '-soft'] = mix(draft[key], draft.surface, isDark ? .18 : .08);
            }
        });

        return output;
    };

    var renderRule = function (selector, variables) {
        var declarations = Object.keys(variables).map(function (name) {
            return '    ' + name + ': ' + variables[name] + ';';
        });
        return selector + ' {\n' + declarations.join('\n') + '\n}';
    };

    var csrfBody = function (data) {
        var params = new URLSearchParams();
        var paramMeta = document.querySelector('meta[name="csrf-param"]');
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (paramMeta && tokenMeta) {
            params.append(paramMeta.content, tokenMeta.content);
        }
        Object.keys(data).forEach(function (key) {
            params.append(key, data[key]);
        });
        return params;
    };

    var notify = function (type, message) {
        if (window.sx && window.sx.notify && typeof window.sx.notify[type] === 'function') {
            window.sx.notify[type](message);
        }
    };

    var findOpener = function (layer) {
        var scope = layer.getAttribute('data-sx-theme-customizer-scope') === 'upa' ? 'upa' : 'admin';
        var buttons = document.querySelectorAll('[data-sx-theme-customizer-lazy]');
        var opener = null;
        Array.prototype.some.call(buttons, function (button) {
            var config = {};
            try {
                config = JSON.parse(button.getAttribute('data-sx-theme-customizer-config') || '{}');
            } catch (error) {
                config = {};
            }
            if ((config.scope === 'upa' ? 'upa' : 'admin') === scope) {
                opener = button;
                return true;
            }
            return false;
        });
        return opener;
    };

    var init = function (layer, opener) {
        if (layer.getAttribute('data-sx-theme-customizer-ready') === 'true') {
            return layer._sxThemeCustomizer;
        }
        layer.setAttribute('data-sx-theme-customizer-ready', 'true');

        opener = opener || findOpener(layer);
        var panel = layer.querySelector('.sx-theme-customizer__panel');
        var modeLabel = layer.querySelector('[data-sx-theme-customizer-mode-label]');
        var closeButtons = layer.querySelectorAll('[data-sx-theme-customizer-close]');
        var modeButtons = layer.querySelectorAll('[data-sx-theme-customizer-mode]');
        var headerModeButtons = layer.querySelectorAll('[data-sx-theme-header]');
        var headerSurface = document.querySelector('.sx-shell-header__surface');
        var colorInputs = layer.querySelectorAll('[data-sx-theme-color]');
        var hexInputs = layer.querySelectorAll('[data-sx-theme-hex]');
        var saveButton = layer.querySelector('[data-sx-theme-customizer-save]');
        var saveDefaultButton = layer.querySelector('[data-sx-theme-customizer-save-default]');
        var resetButton = layer.querySelector('[data-sx-theme-customizer-reset]');
        var config = {};
        var snapshots = {};
        var drafts = {};
        var headerDrafts = {};
        var isOpen = false;
        var previousFocus = null;

        try {
            config = JSON.parse(layer.getAttribute('data-sx-theme-customizer-config') || '{}');
        } catch (error) {
            config = {};
        }
        headerDrafts = clone(config.headerModes || {light: 'dark', dark: 'dark'});

        document.body.appendChild(layer);

        var currentMode = function () {
            return window.sxTheme ? window.sxTheme.getMode() : 'light';
        };

        var capture = function (mode) {
            if (snapshots[mode]) {
                return;
            }
            var styles = window.getComputedStyle(root);
            var base = {};
            var output = {};

            Object.keys(baseVariables).forEach(function (key) {
                var value = normalizeHex(styles.getPropertyValue(baseVariables[key]));
                if (value) {
                    base[key] = value;
                }
            });
            outputNames.forEach(function (name) {
                output[name] = styles.getPropertyValue(name).trim();
            });
            snapshots[mode] = {base: base, output: output};
            drafts[mode] = clone(base);
        };

        var renderPreview = function () {
            var style = document.getElementById(previewStyleId);
            var rules = [];
            if (!style) {
                style = document.createElement('style');
                style.id = previewStyleId;
                document.head.appendChild(style);
            }
            if (snapshots.light) {
                rules.push(renderRule(
                    'html[data-sx-theme="light"]',
                    expand(snapshots.light, drafts.light, 'light')
                ));
            }
            if (snapshots.dark) {
                rules.push(renderRule(
                    'html[data-sx-theme="dark"]',
                    expand(snapshots.dark, drafts.dark, 'dark')
                ));
            }
            style.textContent = rules.join('\n\n');
            if (headerSurface) {
                var mode = currentMode();
                var headerMode = headerDrafts[mode] || 'dark';
                headerSurface.setAttribute(
                    'data-sx-header-preview',
                    headerMode === 'theme' ? mode : headerMode
                );
            }
        };

        var removePreview = function () {
            var style = document.getElementById(previewStyleId);
            if (style && style.parentNode) {
                style.parentNode.removeChild(style);
            }
            if (headerSurface) {
                headerSurface.removeAttribute('data-sx-header-preview');
            }
        };

        var fillFields = function () {
            var mode = currentMode();
            capture(mode);
            var draft = drafts[mode];
            var activeModeButton = null;

            Array.prototype.forEach.call(modeButtons, function (button) {
                var active = button.getAttribute('data-sx-theme-customizer-mode') === mode;
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
                if (active) {
                    activeModeButton = button;
                }
            });
            modeLabel.textContent = activeModeButton ? activeModeButton.textContent.trim() : mode;

            Array.prototype.forEach.call(colorInputs, function (input) {
                var key = input.getAttribute('data-sx-theme-color');
                input.value = draft[key];
            });
            Array.prototype.forEach.call(hexInputs, function (input) {
                var key = input.getAttribute('data-sx-theme-hex');
                input.value = draft[key];
                input.classList.remove('is-invalid');
            });
            Array.prototype.forEach.call(headerModeButtons, function (button) {
                var active = button.getAttribute('data-sx-theme-header') === (headerDrafts[mode] || 'dark');
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        };

        var open = function () {
            if (isOpen || !opener) {
                return;
            }
            previousFocus = document.activeElement;
            capture(currentMode());
            fillFields();
            isOpen = true;
            layer.hidden = false;
            opener.setAttribute('aria-expanded', 'true');
            root.classList.add('sx-theme-customizer-is-open');
            document.body.classList.add('sx-theme-customizer-is-open');
            window.requestAnimationFrame(function () {
                layer.classList.add('is-open');
                panel.focus({preventScroll: true});
            });
        };

        var close = function () {
            if (!isOpen) {
                return;
            }
            isOpen = false;
            layer.classList.remove('is-open');
            opener.setAttribute('aria-expanded', 'false');
            root.classList.remove('sx-theme-customizer-is-open');
            document.body.classList.remove('sx-theme-customizer-is-open');
            removePreview();
            window.setTimeout(function () {
                layer.hidden = true;
                snapshots = {};
                drafts = {};
                headerDrafts = clone(config.headerModes || {light: 'dark', dark: 'dark'});
                if (previousFocus && previousFocus.focus) {
                    previousFocus.focus();
                }
            }, 220);
        };

        var setMode = function (mode) {
            if (window.sxTheme) {
                window.sxTheme.setMode(mode);
            }
            window.requestAnimationFrame(function () {
                capture(mode);
                fillFields();
                renderPreview();
            });
        };

        var updateColor = function (key, value) {
            var normalized = normalizeHex(value);
            var mode = currentMode();
            if (!normalized) {
                return false;
            }
            drafts[mode][key] = normalized;
            renderPreview();
            return true;
        };

        var request = function (url, mode, palette) {
            if (!url) {
                notify('error', 'Theme settings endpoint is not configured.');
                return Promise.resolve(false);
            }
            layer.classList.add('is-busy');
            return window.fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: csrfBody({
                    mode: mode,
                    palette: JSON.stringify(palette || {}),
                    headerMode: headerDrafts[mode] || 'dark'
                })
            }).then(function (response) {
                return response.json();
            }).then(function (result) {
                if (!result.success) {
                    throw new Error(result.message || 'Theme settings could not be saved.');
                }
                window.location.reload();
            }).catch(function (error) {
                layer.classList.remove('is-busy');
                notify('error', error.message || 'Theme settings could not be saved.');
                return false;
            });
        };

        Array.prototype.forEach.call(closeButtons, function (button) {
            button.addEventListener('click', close);
        });
        Array.prototype.forEach.call(modeButtons, function (button) {
            button.addEventListener('click', function () {
                setMode(button.getAttribute('data-sx-theme-customizer-mode'));
            });
        });
        Array.prototype.forEach.call(headerModeButtons, function (button) {
            button.addEventListener('click', function () {
                headerDrafts[currentMode()] = button.getAttribute('data-sx-theme-header');
                fillFields();
                renderPreview();
            });
        });
        Array.prototype.forEach.call(colorInputs, function (input) {
            input.addEventListener('input', function () {
                var key = input.getAttribute('data-sx-theme-color');
                if (updateColor(key, input.value)) {
                    var hexInput = layer.querySelector('[data-sx-theme-hex="' + key + '"]');
                    hexInput.value = normalizeHex(input.value);
                    hexInput.classList.remove('is-invalid');
                }
            });
        });
        Array.prototype.forEach.call(hexInputs, function (input) {
            input.addEventListener('input', function () {
                var key = input.getAttribute('data-sx-theme-hex');
                var valid = updateColor(key, input.value);
                input.classList.toggle('is-invalid', !valid);
                if (valid) {
                    layer.querySelector('[data-sx-theme-color="' + key + '"]').value = normalizeHex(input.value);
                }
            });
            input.addEventListener('blur', function () {
                var normalized = normalizeHex(input.value);
                if (normalized) {
                    input.value = normalized;
                }
            });
        });

        saveButton.addEventListener('click', function () {
            request(config.saveUrl, currentMode(), drafts[currentMode()]);
        });
        if (saveDefaultButton) {
            saveDefaultButton.addEventListener('click', function () {
                if (!window.confirm(saveDefaultButton.textContent.trim() + '?')) {
                    return;
                }
                request(config.saveDefaultUrl, currentMode(), drafts[currentMode()]);
            });
        }
        resetButton.addEventListener('click', function () {
            request(config.resetUrl, currentMode(), {});
        });

        document.addEventListener('keydown', function (event) {
            if (!isOpen) {
                return;
            }
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                return;
            }
            if (event.key !== 'Tab') {
                return;
            }
            var focusable = panel.querySelectorAll(
                'button:not([disabled]), input:not([disabled]), [href], [tabindex]:not([tabindex="-1"])'
            );
            if (!focusable.length) {
                return;
            }
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        layer._sxThemeCustomizer = {
            open: open,
            close: close,
            isOpen: function () {
                return isOpen;
            }
        };
        return layer._sxThemeCustomizer;
    };

    var boot = function () {
        Array.prototype.forEach.call(
            document.querySelectorAll('[data-sx-theme-customizer]'),
            function (layer) {
                init(layer);
            }
        );
    };

    window.sxThemeCustomizer = {
        boot: boot,
        open: function (opener) {
            var config = {};
            try {
                config = JSON.parse(opener.getAttribute('data-sx-theme-customizer-config') || '{}');
            } catch (error) {
                config = {};
            }
            var scope = config.scope === 'upa' ? 'upa' : 'admin';
            var layer = document.querySelector(
                '[data-sx-theme-customizer][data-sx-theme-customizer-scope="' + scope + '"]'
            );
            if (!layer) {
                return;
            }
            var api = init(layer, opener);
            if (api.isOpen()) {
                api.close();
            } else {
                api.open();
            }
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})(window, document);
