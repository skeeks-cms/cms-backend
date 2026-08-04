<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\helpers;

use yii\helpers\Html;

/**
 * Lightweight inline SVG icons for shared backend controls.
 *
 * Icons inherit the current text color and do not require an icon font or a
 * separate HTTP request. Names are semantic so their artwork may evolve
 * without changing controller and view code.
 */
final class BackendIcon
{
    private const ICONS = [
        'search' => '<circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.65-3.65"></path>',
        'settings' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.09a2 2 0 0 1 1 1.74v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path><path d="M12 15V3"></path>',
        'expand' => '<path d="M8 3H5a2 2 0 0 0-2 2v3"></path><path d="M16 3h3a2 2 0 0 1 2 2v3"></path><path d="M8 21H5a2 2 0 0 1-2-2v-3"></path><path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>',
        'plus' => '<path d="M12 5v14"></path><path d="M5 12h14"></path>',
        'close' => '<path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>',
        'move-vertical' => '<path d="m8 7 4-4 4 4"></path><path d="M12 3v18"></path><path d="m8 17 4 4 4-4"></path>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path>',
        'building' => '<rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M9 21v-4h6v4"></path><path d="M8 7h.01"></path><path d="M12 7h.01"></path><path d="M16 7h.01"></path><path d="M8 11h.01"></path><path d="M12 11h.01"></path><path d="M16 11h.01"></path>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"></path>',
        'chevron-right' => '<path d="m9 18 6-6-6-6"></path>',
        'credit-card' => '<rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path>',
        'external-link' => '<path d="M15 3h6v6"></path><path d="m10 14 11-11"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>',
        'handshake' => '<path d="m11 17 2 2a2 2 0 0 0 3-3"></path><path d="m14 14 2.5 2.5a2 2 0 0 0 3-3L15 9"></path><path d="m2 11 5-5 4 1 4-1 7 7"></path><path d="m7 6-5 5"></path><path d="m15 9-3 3a2 2 0 0 1-3-3l2-2"></path>',
        'info' => '<circle cx="12" cy="12" r="9"></circle><path d="M12 11v5"></path><path d="M12 8h.01"></path>',
        'invoice' => '<path d="M6 2h9l3 3v17l-3-2-3 2-3-2-3 2z"></path><path d="M14 2v4h4"></path><path d="M9 10h6"></path><path d="M9 14h6"></path>',
        'lock' => '<rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path>',
        'logout' => '<path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"></path>',
        'moon' => '<path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"></path>',
        'palette' => '<circle cx="13.5" cy="6.5" r=".5" fill="currentColor" stroke="none"></circle><circle cx="17.5" cy="10.5" r=".5" fill="currentColor" stroke="none"></circle><circle cx="8.5" cy="7.5" r=".5" fill="currentColor" stroke="none"></circle><circle cx="6.5" cy="12.5" r=".5" fill="currentColor" stroke="none"></circle><path d="M12 3a9 9 0 0 0 0 18h1.5a1.5 1.5 0 0 0 1.1-2.5 1.5 1.5 0 0 1 1.1-2.5H18a3 3 0 0 0 3-3 10 10 0 0 0-9-10z"></path>',
        'play' => '<path d="M8 5v14l11-7z" fill="currentColor" stroke="none"></path>',
        'refresh' => '<path d="M17.65 6.35c-1.63-1.63-3.94-2.57-6.48-2.31-3.67.37-6.69 3.35-7.1 7.02C3.52 15.91 7.27 20 12 20c3.19 0 5.93-1.87 7.21-4.56.32-.67-.16-1.44-.9-1.44-.37 0-.72.2-.88.53-1.13 2.43-3.84 3.97-6.8 3.31-2.22-.49-4.01-2.3-4.48-4.52C5.31 9.44 8.26 6 12 6c1.66 0 3.14.69 4.22 1.78l-1.51 1.51c-.63.63-.19 1.71.7 1.71H19c.55 0 1-.45 1-1V6.41c0-.89-1.08-1.34-1.71-.71l-.64.65z" fill="currentColor" stroke="none"></path>',
        'star' => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8-6.2-3.2L5.8 21 7 14.2l-5-4.9 6.9-1z"></path>',
        'stop' => '<rect x="6" y="6" width="12" height="12" rx="1" fill="currentColor" stroke="none"></rect>',
        'sun' => '<circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.42 1.42"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>',
        'tasks' => '<path d="M9 6h11"></path><path d="M9 12h11"></path><path d="M9 18h11"></path><path d="m3 6 1 1 2-2"></path><path d="m3 12 1 1 2-2"></path><path d="m3 18 1 1 2-2"></path>',
        'tools' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"></path>',
        'user' => '<circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
    ];

    /**
     * @param string $name
     * @param array $options
     * @return string
     */
    public static function render($name, array $options = [])
    {
        if (!isset(self::ICONS[$name])) {
            throw new \InvalidArgumentException("Unknown backend icon: {$name}");
        }

        $label = isset($options['aria-label']) ? (string)$options['aria-label'] : '';
        $size = isset($options['size']) ? (int)$options['size'] : 20;
        unset($options['size']);

        Html::addCssClass($options, ['sx-icon', 'sx-icon--'.$name]);
        $options = array_merge([
            'viewBox' => '0 0 24 24',
            'width' => $size,
            'height' => $size,
            'fill' => 'none',
            'stroke' => 'currentColor',
            'stroke-width' => 2,
            'stroke-linecap' => 'round',
            'stroke-linejoin' => 'round',
            'focusable' => 'false',
        ], $options);

        if ($label === '') {
            $options['aria-hidden'] = 'true';
        } else {
            $options['role'] = 'img';
        }

        return Html::tag('svg', self::ICONS[$name], $options);
    }
}
