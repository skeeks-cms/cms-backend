<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendShellHeaderAsset;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Shared semantic header frame with product-provided content slots.
 */
class BackendShellHeaderWidget extends Widget
{
    /** @var string */
    public $brand = '';

    /** @var string */
    public $context = '';

    /** @var string */
    public $actions = '';

    /**
     * Terminal profile/account content rendered inside the actions region.
     *
     * Keeping the profile in a separate slot lets every product compose the
     * same semantic header parts without introducing another layout wrapper.
     *
     * @var string
     */
    public $profile = '';

    /** @var array */
    public $options = [];

    /** @var array */
    public $surfaceOptions = [];

    /** @var array */
    public $navOptions = [];

    /** @var array */
    public $brandOptions = [];

    /** @var array */
    public $contextOptions = [];

    /** @var array */
    public $actionsOptions = [];

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = 'js-header';
        }

        Html::addCssClass($this->options, [
            'sx-header',
            'sx-header--sticky-top',
            'sx-shell-header',
        ]);
        Html::addCssClass($this->surfaceOptions, 'sx-shell-header__surface');
        Html::addCssClass($this->navOptions, 'sx-shell-header__nav');
        Html::addCssClass($this->brandOptions, 'sx-shell-header__brand');
        Html::addCssClass($this->contextOptions, 'sx-shell-header__context');
        Html::addCssClass($this->actionsOptions, 'sx-shell-header__actions');
    }

    /**
     * @return string
     */
    public function run()
    {
        BackendShellHeaderAsset::register($this->view);

        return $this->render('backend-shell-header', [
            'brand'          => $this->brand,
            'context'        => $this->context,
            'actions'        => $this->actions,
            'profile'        => $this->profile,
            'options'        => $this->options,
            'surfaceOptions' => $this->surfaceOptions,
            'navOptions'     => $this->navOptions,
            'brandOptions'   => $this->brandOptions,
            'contextOptions' => $this->contextOptions,
            'actionsOptions' => $this->actionsOptions,
        ]);
    }
}
