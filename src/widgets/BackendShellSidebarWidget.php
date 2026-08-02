<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendShellMenuAsset;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Shared semantic sidebar frame with product-provided content slots.
 */
class BackendShellSidebarWidget extends Widget
{
    /**
     * @var string
     */
    public $beforeMenu = '';

    /**
     * @var string
     */
    public $menu = '';

    /**
     * @var string
     */
    public $afterMenu = '';

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $innerOptions = [];

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = 'sideNav';
        }

        Html::addCssClass($this->options, [
            'sx-sidebar',
            'sx-shell-sidebar',
        ]);
        Html::addCssClass($this->innerOptions, 'sx-sidebar-inner');
    }

    /**
     * @return string
     */
    public function run()
    {
        BackendShellMenuAsset::register($this->view);

        return $this->render('backend-shell-sidebar', [
            'beforeMenu'  => $this->beforeMenu,
            'menu'        => $this->menu,
            'afterMenu'   => $this->afterMenu,
            'options'     => $this->options,
            'innerOptions' => $this->innerOptions,
        ]);
    }
}
