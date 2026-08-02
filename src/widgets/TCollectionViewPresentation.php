<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Shared presentation behavior for table and item collection renderers.
 *
 * The consuming widget must provide dataProvider, options and view.
 */
trait TCollectionViewPresentation
{
    /**
     * Rich empty state configuration or false to use the renderer default.
     *
     * @var array|false
     */
    public $emptyState = false;

    /**
     * Optional shared toolbar content for collection renderers.
     *
     * @var string|callable
     */
    public $collectionToolbarLeft = '';

    /**
     * @var string|callable
     */
    public $collectionToolbarRight = '';

    /**
     * @return void
     */
    protected function initCollectionViewPresentation()
    {
        Html::addCssClass($this->options, 'sx-collection-view');
    }

    /**
     * Adds shared semantic classes without replacing controller-supplied item
     * or row options. Yii GridView and ListView use the same callable
     * signature for these options.
     *
     * @param array|callable $options
     * @param array|string $classes
     * @return array|callable
     */
    protected function normalizeCollectionItemOptions($options, $classes = [])
    {
        $classes = array_merge(['sx-collection-item'], (array)$classes);

        if (is_callable($options)) {
            $configuredOptions = $options;

            return static function ($model, $key, $index, $widget) use ($configuredOptions, $classes) {
                $resolvedOptions = (array)call_user_func(
                    $configuredOptions,
                    $model,
                    $key,
                    $index,
                    $widget
                );

                foreach ($classes as $class) {
                    Html::addCssClass($resolvedOptions, $class);
                }

                return $resolvedOptions;
            };
        }

        $options = (array)$options;
        foreach ($classes as $class) {
            Html::addCssClass($options, $class);
        }

        return $options;
    }

    /**
     * @return bool
     */
    public function isRichEmptyState()
    {
        return $this->emptyState !== false
            && $this->dataProvider
            && (int)$this->dataProvider->getCount() === 0;
    }

    /**
     * @return string
     */
    protected function renderCollectionEmptyState()
    {
        BackendAsset::register($this->view);
        Html::addCssClass($this->options, 'sx-collection-is-empty');

        return EmptyStateWidget::widget([
            'config' => (array)$this->emptyState,
        ]);
    }

    /**
     * @param string|callable|null $left
     * @param string|callable|null $right
     * @param array $options
     * @param array $startOptions
     * @param array $endOptions
     * @return string
     */
    public function renderCollectionToolbar(
        $left = null,
        $right = null,
        array $options = [],
        array $startOptions = [],
        array $endOptions = []
    ) {
        $left = $this->resolveCollectionContent(
            $left === null ? $this->collectionToolbarLeft : $left
        );
        $right = $this->resolveCollectionContent(
            $right === null ? $this->collectionToolbarRight : $right
        );

        if ($left === '' && $right === '') {
            return '';
        }

        Html::addCssClass($options, 'sx-collection-toolbar');
        Html::addCssClass($startOptions, 'sx-collection-toolbar__start');
        Html::addCssClass($endOptions, 'sx-collection-toolbar__end');

        return Html::tag(
            'div',
            Html::tag('div', $left, $startOptions).
            Html::tag('div', $right, $endOptions),
            $options
        );
    }

    /**
     * Shared page-size control used by table and list renderers.
     *
     * @return string
     */
    public function renderCollectionPerPage()
    {
        $pagination = $this->dataProvider->getPagination();

        if (!$pagination || $this->dataProvider->getTotalCount() <= $pagination->getPageSize()) {
            return '';
        }

        $min = (int)$pagination->pageSizeLimit[0];
        $max = (int)$pagination->pageSizeLimit[1];
        $step = 5;

        if ($max - $min > 50) {
            $step = max(1, (int)round(($max - $min) / 30));
        }

        $items = [];
        for ($i = $min; $i <= $max; $i++) {
            if ($i > 0 && $i % $step === 0) {
                $items[$i] = $i;
            }
        }

        $items[$max] = $max;
        $items[$pagination->pageSize] = $pagination->pageSize;
        ksort($items);

        $id = $this->id.'-per-page';
        $get = \Yii::$app->request->get();
        ArrayHelper::remove($get, $pagination->pageSizeParam);
        $get[$pagination->pageSizeParam] = '';
        $url = '/'.\Yii::$app->request->pathInfo.'?'.http_build_query($get);

        $this->view->registerJs(<<<JS
(function(sx, $, _)
{
    sx.classes.GridPerPage = sx.classes.Component.extend({

        _onDomReady: function()
        {
            var self = this;
            var JSelect = $("#" + this.get('id'));
            JSelect.on("change", function()
            {
                var JLink = $("<a>", {
                    'href' : self.get('url') + $(this).val(),
                    'style' : 'display: none;',
                }).text('link');

                $(this).closest('form').append(JLink);
                JLink.click();
            });
        }
    });

    new sx.classes.GridPerPage({
        'id' : '{$id}',
        'url' : '{$url}'
    });
})(sx, sx.$, sx._);
JS
        );

        return Html::tag(
            'div',
            Html::tag(
                'form',
                Html::tag(
                    'span',
                    \Yii::t('skeeks/cms', 'On the page').':',
                    ['class' => 'per-page-label']
                ).
                Html::dropDownList(
                    $pagination->pageSizeParam,
                    $pagination->pageSize,
                    $items,
                    ['id' => $id]
                ),
                [
                    'method' => 'get',
                    'action' => $url,
                ]
            ),
            ['class' => 'sx-per-page']
        );
    }

    /**
     * @param mixed $content
     * @return string
     */
    protected function resolveCollectionContent($content)
    {
        if ($content instanceof \Closure) {
            $content = call_user_func($content, $this);
        }

        return (string)$content;
    }
}
