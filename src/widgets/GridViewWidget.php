<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\modules\admin\assets\AdminGridAsset;
use skeeks\cms\widgets\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class GridViewWidget
 * @package skeeks\cms\backend\widgets
 */
class GridViewWidget extends GridView
{
    public $tableOptions = [
        'class' => 'table-striped'
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
                      <div class='sx-table-wrapper table-responsive'>
                          {items}\n
                      </div>
                      {afterTable}
                      <div class='row sx-table-additional'>
                          <div class='col-md-12'>
                      \n<div class='pull-left'>{pager}</div>
                      \n<div class='pull-left'>{perPage}</div>
                      \n<!--<div class='pull-left'>{sorter}</div>-->
                        <div class='pull-right'>{summary}</div></div>
                      </div>";


    public function init()
    {
        parent::init();

        if ($this->defaultTableCssClasses) {
            foreach ((array) $this->defaultTableCssClasses as $cssClass)
            {
                Html::addCssClass($this->tableOptions, $cssClass);
            }
        }

        Html::addCssClass($this->options, 'sx-grid-view');
    }
    /**
     * @param string $name
     * @return bool|string
     */
    public function renderSection($name)
    {
        switch ($name) {
            case "{beforeTable}":
                return $this->renderBeforeTable();
            case "{afterTable}":
                return $this->renderAfterTable();
            case "{perPage}":
                return $this->renderPerPage();
            default:
                return parent::renderSection($name);
        }
    }
    /**
     * @return string
     */
    public function renderBeforeTable()
    {
        if ($this->beforeTableLeft || $this->beforeTableRight) {

            if ($this->beforeTableLeft instanceof \Closure) {
                $this->beforeTableLeft = call_user_func($this->beforeTableLeft, $this);
            }

            if ($this->beforeTableRight instanceof \Closure) {
                $this->beforeTableRight = call_user_func($this->beforeTableRight, $this);
            }

            return <<<HTML
        <div class='sx-before-table'>
            <div class='pull-left'>{$this->beforeTableLeft}</div>
            <div class='pull-right'>{$this->beforeTableRight}</div>
          </div>
HTML;
        } else {
            return '';
        }

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
            return "<div class='sx-after-table'>
                        <div class='pull-left'>{$this->afterTableLeft}</div>
                        <div class='pull-right'>{$this->afterTableRight}</div>
                    </div>";
        } else {
            return "";
        }

    }
    /**
     * @return string
     */
    public function renderPerPage()
    {
        $pagination = $this->dataProvider->getPagination();

        $min = $pagination->pageSizeLimit[0];
        $max = $pagination->pageSizeLimit[1];
        
        $step = 5; 
        if ($max - $min > 50) {
            $step = ($max - $min) / 30;
            $step = round($step);
        }

        $items = [];
        $i = 0;
        for ($i >= $min; $i <= $max; $i++) {
            if ($i % $step == 0 && $i > 0) {
                $items[$i] = $i;
            }
        }
        
        if ($i != $max) {
            $items[$max] = $max;
        }

        $id = $this->id."-per-page";
        
        

        $get = \Yii::$app->request->get();
        ArrayHelper::remove($get, $pagination->pageSizeParam);
        $get[$pagination->pageSizeParam] = "";

        $url = '/'.\Yii::$app->request->pathInfo."?".http_build_query($get);

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
                $(this).val();

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


        return "<div class='sx-per-page'><form method='get' action='".$url."'> <span class='per-page-label'>".\Yii::t('skeeks/cms', 'On the page').":</span>"
            .Html::dropDownList($pagination->pageSizeParam, [$pagination->pageSize], $items, [
                'id' => $id,
            ])."</form></div>";
    }
}