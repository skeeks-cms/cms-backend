<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendAsset;
use skeeks\cms\modules\admin\assets\AdminGridAsset;
use skeeks\cms\widgets\GridView;
use yii\helpers\Html;

/**
 * Class GridViewWidget
 * @package skeeks\cms\backend\widgets
 */
class GridViewWidget extends GridView
{
    use TCollectionViewPresentation;

    /**
     * Visual collection variant.
     *
     * The "client" variant keeps the semantics and capabilities of GridView,
     * but gives rows a calmer, service-oriented presentation suitable for
     * customer cabinets.
     *
     * @var string|null
     */
    public $presentation;

    public $tableOptions = [
        //'class' => 'table-striped'
    ];

    public $defaultTableCssClasses = [
        'table', 'sx-table'
    ];

    public $options = [
        'class' => 'grid-view'
    ];

    /**
     * @var string|callable
     */
    public $afterTableLeft = "";
    /**
     * @var string|callable
     */
    public $afterTableRight = "";
    /**
     * @var string|callable
     */
    public $beforeTableLeft = "";
    /**
     * @var string|callable
     */
    public $beforeTableRight = "";
    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     * - `{beforeTable}`: the pager. See [[renderPager()]].
     * - `{afterTable}`: the pager. See [[renderPager()]].
     */
    public $layout = "{beforeTable}\n
                      <div class='sx-collection-body sx-table-wrapper table-responsive'>
                          {items}\n
                      </div>
                      {afterTable}
                      <div class='sx-collection-footer sx-table-additional'>
                          <div class='sx-collection-footer__inner col-md-12'>
                      \n<div class='sx-collection-footer__pager pull-left'>{pager}</div>
                      \n<div class='sx-collection-footer__per-page pull-left'>{perPage}</div>
                      \n<!--<div class='pull-left'>{sorter}</div>-->
                        <div class='sx-collection-footer__summary pull-right'>{summary}</div></div>
                      </div>";


    public function init()
    {
        parent::init();
        BackendAsset::register($this->getView());

        if ($this->defaultTableCssClasses) {
            foreach ((array) $this->defaultTableCssClasses as $cssClass)
            {
                Html::addCssClass($this->tableOptions, $cssClass);
            }
        }

        foreach (['grid-view', 'sx-grid-view', 'sx-backend-grid'] as $cssClass) {
            Html::addCssClass($this->options, $cssClass);
        }
        if ($this->presentation) {
            Html::addCssClass($this->options, 'sx-backend-grid--'.$this->presentation);
        }
        $this->rowOptions = $this->normalizeCollectionItemOptions(
            $this->rowOptions,
            ['sx-grid-row']
        );
        $this->initCollectionViewPresentation();
    }
    /**
     * @param string $name
     * @return bool|string
     */
    public function renderSection($name)
    {
        if ($this->isRichEmptyState() && in_array($name, [
            '{beforeTable}',
            '{afterTable}',
            '{pager}',
            '{perPage}',
            '{summary}',
        ], true)) {
            return '';
        }

        switch ($name) {
            case "{beforeTable}":
                return $this->renderBeforeTable();
            case "{afterTable}":
                return $this->renderAfterTable();
            case "{perPage}":
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
        if (!$this->isRichEmptyState()) {
            return parent::renderItems();
        }

        return $this->renderCollectionEmptyState();
    }
    /**
     * @return string
     */
    public function renderBeforeTable()
    {
        $left = $this->beforeTableLeft ?: $this->collectionToolbarLeft;
        $right = $this->beforeTableRight ?: $this->collectionToolbarRight;

        $toolbar = $this->renderCollectionToolbar($left, $right, [
            'class' => 'sx-before-table',
        ], [
            'class' => 'sx-before-table-left',
        ], [
            'class' => 'sx-before-table-right',
        ]);

        return $toolbar;
    }
    /**
     * @return string
     */
    public function renderAfterTable()
    {
        if ($this->afterTableLeft || $this->afterTableRight) {
            if ($this->afterTableLeft instanceof \Closure) {
                $this->afterTableLeft = call_user_func($this->afterTableLeft, $this);
            }
            if ($this->afterTableRight instanceof \Closure) {
                $this->afterTableRight = call_user_func($this->afterTableRight, $this);
            }

            if ($this->afterTableLeft || $this->afterTableRight) {
                return "<div class='sx-after-table'>
                    <div class='pull-left'>{$this->afterTableLeft}</div>
                    <div class='pull-right'>{$this->afterTableRight}</div>
                </div>";
            }

        } else {
            return "";
        }

    }
}
