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
 * @property string $slideNavClasses
 * @property string $normalizedThemeMode
 */
class BackendTheme extends Theme
{
    const THEME_MODE_LIGHT = 'light';
    const THEME_MODE_DARK = 'dark';

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
     * Legacy configuration alias retained for existing project themes.
     *
     * @deprecated Configure themeMode instead.
     * @var string
     */
    public $color_scheme = 'multi';

    /** @var string */
    public $logoTitle = 'SkeekS CMS';

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

        $bundles[\yii\web\JqueryAsset::class] = [
            'class' => BackendJqueryAsset::class,
        ];
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

    /**
     * @return string
     */
    public function getSlideNavClasses()
    {
        return 'sx-shell-sidebar--default';
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
}
