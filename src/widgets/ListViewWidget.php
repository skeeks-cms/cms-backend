<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendAsset;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;

/**
 * Standard div/items collection renderer for backend and customer cabinets.
 */
class ListViewWidget extends ListView
{
    use TCollectionViewPresentation;

    /**
     * Model used to create an ActiveDataProvider when dataProvider is omitted.
     *
     * @var string|null
     */
    public $modelClassName;

    public $pageParam = 'page';
    public $pageSizeParam = 'per-page';
    public $defaultPageSize = 20;
    public $pageSizeLimitMin = 1;
    public $pageSizeLimitMax = 50;
    public $defaultOrder = [];
    public $sortAttributes = [];

    public $options = [
        'class' => 'sx-list-view',
    ];

    public $itemOptions = [
        'class' => 'sx-list-item',
    ];

    /**
     * Keep the configured layout active for empty collections so renderItems()
     * can replace Yii's plain emptyText with the shared rich empty state.
     *
     * @var bool
     */
    public $showOnEmpty = true;

    public $layout = "{collectionToolbar}\n
        <div class=\"sx-collection-body sx-list-items\">{items}</div>\n
        <div class=\"sx-collection-footer sx-list-additional\">
            <div class=\"sx-collection-footer__inner\">
                <div class=\"sx-collection-footer__pager\">{pager}</div>
                <div class=\"sx-collection-footer__per-page\">{perPage}</div>
                <div class=\"sx-collection-footer__summary\">{summary}</div>
            </div>
        </div>";

    /**
     * @return void
     */
    public function init()
    {
        if (!$this->dataProvider) {
            $this->dataProvider = $this->createDataProvider();
        }

        if (is_callable($this->dataProvider)) {
            $dataProvider = $this->dataProvider;
            $this->dataProvider = call_user_func($dataProvider, $this);
        }

        $this->initPagination();
        $this->initSort();

        parent::init();

        BackendAsset::register($this->getView());
        Html::addCssClass($this->options, 'sx-list-view');
        Html::addCssClass($this->options, 'sx-backend-list');
        $this->initItemOptions();
        $this->initCollectionViewPresentation();
    }

    /**
     * Keeps the semantic item hook even when a controller supplies its own
     * itemOptions array or callback.
     *
     * @return void
     */
    protected function initItemOptions()
    {
        $this->itemOptions = $this->normalizeCollectionItemOptions(
            $this->itemOptions,
            ['sx-list-item']
        );
    }

    /**
     * @return ActiveDataProvider|ArrayDataProvider
     */
    protected function createDataProvider()
    {
        $modelClassName = $this->modelClassName;

        if ($modelClassName) {
            return new ActiveDataProvider([
                'query' => $modelClassName::find()
                    ->select([$modelClassName::tableName().'.*']),
            ]);
        }

        return new ArrayDataProvider([
            'allModels' => [],
        ]);
    }

    /**
     * @return void
     */
    protected function initPagination()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false) {
            return;
        }

        $pagination->defaultPageSize = $this->defaultPageSize;
        $pagination->pageParam = $this->pageParam;
        $pagination->pageSizeParam = $this->pageSizeParam;
        $pagination->pageSizeLimit = [
            (int)$this->pageSizeLimitMin,
            (int)$this->pageSizeLimitMax,
        ];
    }

    /**
     * @return void
     */
    protected function initSort()
    {
        $sort = $this->dataProvider->getSort();
        if ($sort === false) {
            return;
        }

        $sort->attributes = ArrayHelper::merge($sort->attributes, $this->sortAttributes);

        $defaultOrder = (array)$this->defaultOrder;
        foreach ($defaultOrder as $attribute => $direction) {
            if (!isset($sort->attributes[$attribute])) {
                unset($defaultOrder[$attribute]);
            }
        }

        $sort->defaultOrder = $defaultOrder;
    }

    /**
     * @param string $name
     * @return bool|string
     */
    public function renderSection($name)
    {
        if ($this->isRichEmptyState() && in_array($name, [
            '{collectionToolbar}',
            '{pager}',
            '{perPage}',
            '{summary}',
            '{sorter}',
        ], true)) {
            return '';
        }

        switch ($name) {
            case '{collectionToolbar}':
                return $this->renderCollectionToolbar();
            case '{perPage}':
                return $this->renderCollectionPerPage();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * @return string
     */
    public function renderItems()
    {
        if ($this->isRichEmptyState()) {
            return $this->renderCollectionEmptyState();
        }

        return parent::renderItems();
    }
}
