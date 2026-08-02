<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use yii\base\Widget;

/**
 * Shared light/dark theme selector for backend-based layouts.
 */
class BackendThemeModeSwitcher extends Widget
{
    /** @var string */
    public $containerClass = '';

    /** @var string */
    public $toggleClass = '';

    /** @var bool */
    public $showLabel = false;

    /**
     * @return string
     */
    public function run()
    {
        $theme = $this->view->theme;
        if (isset($theme->allowClientThemeMode) && !$theme->allowClientThemeMode) {
            return '';
        }

        return $this->render('@skeeks/cms/backend/widgets/views/backend-theme-mode-switcher', [
            'containerClass' => $this->containerClass,
            'toggleClass'    => $this->toggleClass,
            'showLabel'      => $this->showLabel,
        ]);
    }
}
