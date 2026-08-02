<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\assets;

/**
 * Font Awesome compatibility for controllers not yet migrated to BackendIcon.
 *
 * Clean products should prefer inline semantic SVG icons and omit this asset.
 */
class BackendLegacyIconAsset extends \skeeks\cms\base\AssetBundle
{
    public $sourcePath = '@vendor/fortawesome/font-awesome/';

    public $css = [
        'css/all.min.css',
    ];

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        $appendTimestamp = \Yii::$app->assetManager->appendTimestamp;
        \Yii::$app->assetManager->appendTimestamp = false;

        foreach ([
            'webfonts/fa-brands-400.woff2',
            'webfonts/fa-regular-400.woff2',
            'webfonts/fa-solid-900.woff2',
        ] as $fontPath) {
            $view->registerLinkTag([
                'rel'         => 'preload',
                'href'        => self::getAssetUrl($fontPath),
                'as'          => 'font',
                'type'        => 'font/woff2',
                'crossorigin' => 'anonymous',
            ]);
        }

        \Yii::$app->assetManager->appendTimestamp = $appendTimestamp;
    }
}
