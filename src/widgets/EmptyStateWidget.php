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
 * Renderer-independent empty state for backend pages and collections.
 */
class EmptyStateWidget extends Widget
{
    /**
     * Supported keys: title, description, icon, options and action
     * (label, url, icon, options).
     *
     * @var array
     */
    public $config = [];

    /**
     * @return string
     */
    public function run()
    {
        BackendUiAsset::register($this->getView());

        $config = (array)$this->config;
        $options = (array)ArrayHelper::getValue($config, 'options', []);
        Html::addCssClass(
            $options,
            'sx-empty-state sx-collection-empty-state sx-grid-empty-state'
        );
        $options['data-sx-collection-state'] = 'empty';

        $icon = (string)ArrayHelper::getValue($config, 'icon', 'fa fa-inbox');
        $title = (string)ArrayHelper::getValue($config, 'title', '');
        $description = (string)ArrayHelper::getValue($config, 'description', '');
        $action = (array)ArrayHelper::getValue($config, 'action', []);

        $content = Html::tag(
            'span',
            BackendIcon::renderConfigured($icon, ['size' => 28]),
            [
                'class' => 'sx-empty-state__icon'
                    .' sx-collection-empty-state__icon sx-grid-empty-state__icon',
            ]
        );

        if ($title !== '') {
            $content .= Html::tag('h2', Html::encode($title), [
                'class' => 'sx-empty-state__title'
                    .' sx-collection-empty-state__title sx-grid-empty-state__title',
            ]);
        }

        if ($description !== '') {
            $content .= Html::tag('p', Html::encode($description), [
                'class' => 'sx-empty-state__description'
                    .' sx-collection-empty-state__description sx-grid-empty-state__description',
            ]);
        }

        $actionLabel = (string)ArrayHelper::getValue($action, 'label', '');
        $actionUrl = ArrayHelper::getValue($action, 'url');
        if ($actionLabel !== '' && $actionUrl) {
            $actionOptions = (array)ArrayHelper::getValue($action, 'options', []);
            $actionVariant = (string)ArrayHelper::getValue($action, 'variant', 'primary');
            Html::addCssClass(
                $actionOptions,
                'sx-button sx-button--'.$actionVariant
                .' sx-collection-action sx-collection-action--'.$actionVariant
                .' sx-empty-state__action'
                .' sx-collection-empty-state__action sx-grid-empty-state__action'
            );
            $actionIcon = (string)ArrayHelper::getValue($action, 'icon', '');
            $label = $actionIcon
                ? BackendIcon::renderConfigured($actionIcon, ['size' => 16]).' '.Html::encode($actionLabel)
                : Html::encode($actionLabel);
            $content .= Html::a($label, $actionUrl, $actionOptions);
        }

        return Html::tag('section', $content, $options);
    }
}
