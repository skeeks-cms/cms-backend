<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendUiAsset;
use skeeks\cms\backend\helpers\BackendIcon;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Canonical heading for a backend section or collection page.
 *
 * Titles, descriptions and action labels are plain text. Action URLs and
 * options are resolved by the owning action/controller before rendering.
 * Icon values may be semantic BackendIcon names or legacy icon-font classes.
 */
class BackendSectionHeader extends Widget
{
    /** @var string Plain page title. */
    public $title = '';

    /** @var string Plain supporting copy. */
    public $description = '';

    /** @var string Semantic icon name or legacy icon-font classes. */
    public $icon = '';

    /** @var array Resolved page actions. */
    public $actions = [];

    /** @var array Root header options. */
    public $options = [];

    public function init()
    {
        parent::init();

        Html::addCssClass($this->options, 'sx-collection-page-header sx-grid-page-header');
        BackendUiAsset::register($this->getView());
    }

    public function run()
    {
        $copy = '';
        if ($this->icon !== '') {
            $copy .= Html::tag('span', BackendIcon::renderConfigured($this->icon, ['size' => 22]), [
                'class' => 'sx-grid-page-header__icon',
            ]);
        }

        $text = '';
        if ($this->title !== '') {
            $text .= Html::tag('h1', Html::encode($this->title));
        }
        if ($this->description !== '') {
            $text .= Html::tag('p', Html::encode($this->description));
        }
        $copy .= Html::tag('div', $text);

        $content = Html::tag('div', $copy, ['class' => 'sx-grid-page-header__copy']);
        $actions = $this->renderActions();
        if ($actions !== '') {
            $content .= Html::tag('div', $actions, [
                'class' => 'sx-collection-page-header__actions',
            ]);
        }

        return Html::tag('header', $content, $this->options);
    }

    protected function renderActions()
    {
        $result = '';
        foreach ((array)$this->actions as $index => $action) {
            $action = (array)$action;
            $label = (string)ArrayHelper::getValue($action, 'label', '');
            $url = ArrayHelper::getValue($action, 'url');
            if ($label === '' || !$url) {
                continue;
            }

            $variant = (string)ArrayHelper::getValue(
                $action,
                'variant',
                $index === 0 ? 'primary' : 'secondary'
            );
            $options = (array)ArrayHelper::getValue($action, 'options', []);
            Html::addCssClass(
                $options,
                'sx-button sx-button--'.$variant
                .' sx-collection-action sx-collection-action--'.$variant
                .' sx-grid-page-header__action'
            );

            $icon = (string)ArrayHelper::getValue($action, 'icon', '');
            $content = $icon !== ''
                ? BackendIcon::renderConfigured($icon, ['size' => 16]).' '.Html::encode($label)
                : Html::encode($label);
            $result .= Html::a($content, $url, $options);
        }

        return $result;
    }
}
