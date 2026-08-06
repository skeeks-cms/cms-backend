<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\themes;

use skeeks\cms\backend\assets\BackendAppAsset;
use skeeks\cms\backend\assets\BackendBootstrapAsset;
use skeeks\cms\backend\assets\BackendBootstrapPluginAsset;
use skeeks\cms\backend\assets\BackendJqueryAsset;
use skeeks\cms\backend\assets\BackendShellHeaderAsset;
use skeeks\cms\backend\assets\BackendShellMenuAsset;
use skeeks\cms\backend\form\fields\BackendSelectField;
use skeeks\cms\backend\widgets\jui\BackendSortableWidget;
use skeeks\yii2\form\fields\SelectField;
use yii\base\Theme;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Shared theme contract for administration panels and customer cabinets.
 *
 * Product themes should normally override semantic CSS variables, slot views
 * and, when required, the three asset hooks. Unify-specific Bootstrap/jQuery
 * replacements belong to the compatibility subclass, not to this class.
 *
 * @property string $logoSrc
 * @property string $logoHref
 * @property string $headerClasses
 * @property array $headerAppearanceAttributes
 * @property array $normalizedHeaderModes
 * @property string $slideNavClasses
 * @property string $normalizedThemeMode
 * @property string $paletteCss
 */
class BackendTheme extends Theme
{
    const THEME_MODE_LIGHT = 'light';
    const THEME_MODE_DARK = 'dark';
    const HEADER_MODE_THEME = 'theme';
    const HEADER_MODE_LIGHT = 'light';
    const HEADER_MODE_DARK = 'dark';

    public $pathMap = [
        '@app/views' => [
            '@skeeks/cms/backend/views',
        ],
    ];

    /**
     * Root shell asset. A complete product may replace this with a bundle that
     * also provides its functional Bootstrap/JS compatibility layer.
     *
     * @var string
     */
    public $appAssetClass = BackendAppAsset::class;

    /** @var string */
    public $headerAssetClass = BackendShellHeaderAsset::class;

    /** @var string */
    public $leftMenuAssetClass = BackendShellMenuAsset::class;

    /**
     * Explicit mode. Null allows a stored or operating-system preference.
     *
     * @var string|null
     */
    public $themeMode = null;

    /** @var string */
    public $themeModeStorageKey = 'sx-theme-mode';

    /** @var bool */
    public $allowClientThemeMode = true;

    /**
     * Small, validated light/dark palette for a project or future editor.
     * See BackendThemePalette::INPUT_KEYS for the stable storage schema.
     * An empty value keeps the asset defaults and registers no inline CSS.
     *
     * @var array
     */
    public $palette = [];

    /**
     * Theme customizer transport/UI configuration. An empty array keeps the
     * compact mode switcher unchanged. Persistence is supplied by the owning
     * application so cms-backend remains storage-agnostic.
     *
     * @var array
     */
    public $themeCustomizer = [];

    /**
     * Header appearance for each page theme. An empty configuration keeps the
     * shared default: a dark header in both light and dark interfaces.
     * Supported values are dark, light and theme (follow the page theme).
     *
     * @var array
     */
    public $headerModes = [];

    /**
     * Legacy configuration alias retained for existing project themes.
     *
     * @deprecated Configure themeMode instead.
     * @var string
     */
    public $color_scheme = 'multi';

    /** @var string */
    public $logoTitle = 'SkeekS CMS';

    /**
     * Optional logo variants for light and dark header backgrounds. The
     * original logoSrc remains the backward-compatible fallback.
     *
     * @var string
     */
    public $logoSrcLight = '';

    /** @var string */
    public $logoSrcDark = '';

    /** @var string|null */
    protected $_logoSrc;

    /** @var string|null */
    protected $_logoHref;

    /**
     * Hook for a compatibility theme to prepare asset/container mappings.
     * A semantic backend theme needs no global replacements by default.
     */
    public static function initBeforeRender()
    {
        $bundles = \Yii::$app->assetManager->bundles;

        // A client portal may have registered its application assets before a
        // backend action installs the semantic theme. Replacing jQuery at that
        // point would render two copies and detach plugins from sx.$.
        if (!isset(\Yii::$app->view->assetBundles[\yii\web\JqueryAsset::class])) {
            $bundles[\yii\web\JqueryAsset::class] = [
                'class' => BackendJqueryAsset::class,
            ];
        }
        $bundles[\yii\bootstrap\BootstrapAsset::class] = [
            'class' => BackendBootstrapAsset::class,
        ];
        $bundles[\yii\bootstrap\BootstrapPluginAsset::class] = [
            'class' => BackendBootstrapPluginAsset::class,
        ];
        $bundles[\yii\bootstrap4\BootstrapAsset::class] = [
            'class' => BackendBootstrapAsset::class,
        ];
        $bundles[\yii\bootstrap4\BootstrapPluginAsset::class] = [
            'class' => BackendBootstrapPluginAsset::class,
        ];

        \Yii::$app->assetManager->bundles = $bundles;

        \Yii::$container->setDefinitions(ArrayHelper::merge(
            \Yii::$container->definitions,
            [
                SelectField::class => [
                    'class' => BackendSelectField::class,
                ],
                \yii\jui\Sortable::class => [
                    'class' => BackendSortableWidget::class,
                ],
                \yii\bootstrap\ActiveForm::class => [
                    'class' => \yii\bootstrap4\ActiveForm::class,
                ],
                \yii\bootstrap\ActiveField::class => [
                    'class' => \yii\bootstrap4\ActiveField::class,
                ],
                \yii\bootstrap\Alert::class => [
                    'class' => \yii\bootstrap4\Alert::class,
                ],
                \yii\bootstrap\Modal::class => [
                    'class' => \skeeks\bootstrap4\Modal::class,
                ],
                \yii\widgets\LinkPager::class => [
                    'linkOptions' => [
                        'class' => 'page-link',
                    ],
                    'linkContainerOptions' => [
                        'class' => 'page-item',
                    ],
                    'disabledListItemSubTagOptions' => [
                        'class' => 'page-link',
                    ],
                ],
            ]
        ));
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        if ($this->_logoSrc === null) {
            $this->_logoSrc = $this->getDefaultLogoSrc();
        }

        return (string) $this->_logoSrc;
    }

    /**
     * @return string
     */
    protected function getDefaultLogoSrc()
    {
        return '';
    }

    /**
     * @param string|null $src
     * @return $this
     */
    public function setLogoSrc($src)
    {
        $this->_logoSrc = $src;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoHref()
    {
        if (!$this->_logoHref) {
            $this->_logoHref = Url::home();
        }

        return (string) $this->_logoHref;
    }

    /**
     * @param string|null $href
     * @return $this
     */
    public function setLogoHref($href)
    {
        $this->_logoHref = $href;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderClasses()
    {
        return 'sx-shell-header__surface--default';
    }

    /** @return array */
    public function getNormalizedHeaderModes()
    {
        $result = [
            self::THEME_MODE_LIGHT => self::HEADER_MODE_DARK,
            self::THEME_MODE_DARK  => self::HEADER_MODE_DARK,
        ];

        foreach ($result as $themeMode => $defaultHeaderMode) {
            $headerMode = isset($this->headerModes[$themeMode])
                ? $this->headerModes[$themeMode]
                : $defaultHeaderMode;
            if (!in_array($headerMode, [
                self::HEADER_MODE_THEME,
                self::HEADER_MODE_LIGHT,
                self::HEADER_MODE_DARK,
            ], true)) {
                $headerMode = $defaultHeaderMode;
            }
            $result[$themeMode] = $headerMode;
        }

        return $result;
    }

    /** @return array */
    public function getHeaderAppearanceAttributes()
    {
        $modes = $this->getNormalizedHeaderModes();

        return [
            'data-sx-header-light' => $modes[self::THEME_MODE_LIGHT] === self::HEADER_MODE_THEME
                ? self::HEADER_MODE_LIGHT
                : $modes[self::THEME_MODE_LIGHT],
            'data-sx-header-dark' => $modes[self::THEME_MODE_DARK] === self::HEADER_MODE_THEME
                ? self::HEADER_MODE_DARK
                : $modes[self::THEME_MODE_DARK],
        ];
    }

    /**
     * @return string
     */
    public function getSlideNavClasses()
    {
        return 'sx-shell-sidebar--comfortable';
    }

    /**
     * @return string
     */
    public function getNormalizedThemeMode()
    {
        $mode = $this->themeMode;

        if ($mode === null) {
            $mode = $this->color_scheme === self::THEME_MODE_DARK
                ? self::THEME_MODE_DARK
                : self::THEME_MODE_LIGHT;
        }

        if (!in_array($mode, [
            self::THEME_MODE_LIGHT,
            self::THEME_MODE_DARK,
        ], true)) {
            return self::THEME_MODE_LIGHT;
        }

        return $mode;
    }

    /** @return string */
    public function getPaletteCss()
    {
        if (!$this->palette) {
            return '';
        }

        return (new BackendThemePalette($this->palette))->toCss();
    }
}
