<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\modules\admin\assets\AdminGridAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class GridViewWidget
 * @package skeeks\cms\backend\widgets
 */
class GridViewWidget extends \yii\grid\GridView
{
    public $tableOptions = ['class' => 'table table-striped table-bordered sx-table'];
    public $options = ['class' => 'grid-view sx-grid-view'];
    /**
     * @var string
     */
    public $afterTableLeft = "";
    /**
     * @var string
     */
    public $afterTableRight = "";
    /**
     * @var string
     */
    public $beforeTableLeft = "";
    /**
     * @var string
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

        $items = [];
        $i = 0;
        for ($i >= $min; $i <= $max; $i++) {
            if ($i % 5 == 0 && $i > 0) {
                $items[$i] = $i;
            }
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