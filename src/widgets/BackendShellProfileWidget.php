<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendShellHeaderAsset;
use skeeks\cms\backend\helpers\BackendIcon;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Shared account/profile control for backend, Admin and customer cabinets.
 */
class BackendShellProfileWidget extends Widget
{
    /** @var string */
    public $avatarSrc = '';

    /** @var string */
    public $avatarAlt = '';

    /** @var string */
    public $label = '';

    /** @var string Trusted menu item HTML. */
    public $menu = '';

    /** @var array */
    public $options = [];

    /** @var array */
    public $toggleOptions = [];

    /** @var array */
    public $avatarOptions = [];

    /** @var array */
    public $labelOptions = [];

    /** @var array */
    public $menuOptions = [];

    public function init()
    {
        parent::init();

        Html::addCssClass($this->options, ['sx-shell-profile', 'dropdown']);
        Html::addCssClass($this->toggleOptions, ['sx-shell-profile__toggle', 'dropdown-toggle']);
        Html::addCssClass($this->avatarOptions, 'sx-shell-profile__avatar');
        Html::addCssClass($this->labelOptions, ['sx-shell-profile__label', 'sx-shell-hidden-sm-down']);
        Html::addCssClass($this->menuOptions, ['dropdown-menu', 'dropdown-menu-right', 'sx-shell-header__menu']);

        $this->toggleOptions['data-toggle'] = $this->toggleOptions['data-toggle'] ?? 'dropdown';
        $this->toggleOptions['aria-haspopup'] = $this->toggleOptions['aria-haspopup'] ?? 'true';
        $this->toggleOptions['aria-expanded'] = $this->toggleOptions['aria-expanded'] ?? 'false';
        $this->avatarOptions['alt'] = $this->avatarOptions['alt'] ?? $this->avatarAlt;
    }

    public function run()
    {
        BackendShellHeaderAsset::register($this->view);

        if ((string)$this->avatarSrc !== '') {
            $avatar = Html::img($this->avatarSrc, $this->avatarOptions);
        } else {
            $fallbackOptions = $this->avatarOptions;
            unset($fallbackOptions['alt']);
            Html::addCssClass($fallbackOptions, 'sx-shell-profile__avatar--fallback');
            $fallbackOptions['aria-hidden'] = 'true';

            $avatar = Html::tag('span', BackendIcon::render('user', [
                'size' => 18,
            ]), $fallbackOptions);
        }

        $toggle = $avatar
            .Html::tag('span', Html::encode($this->label), $this->labelOptions)
            .BackendIcon::render('chevron-down', [
                'size'  => 10,
                'class' => 'sx-shell-profile__chevron',
            ]);

        $toggleHref = $this->toggleOptions['href'] ?? '#';
        unset($this->toggleOptions['href']);

        return Html::tag(
            'div',
            Html::a($toggle, $toggleHref, $this->toggleOptions).Html::tag('ul', $this->menu, $this->menuOptions),
            $this->options
        );
    }
}
