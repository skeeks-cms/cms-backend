<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\queryfilters\QueryFiltersWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
//class FiltersWidget extends \skeeks\cms\widgets\FiltersWidget {
class FiltersWidget extends QueryFiltersWidget {

    public $viewFile = 'filters';

    public function init()
    {
        $fieldControlls = <<<HTML
<div class="col-sm-2">
    <div class="sx-field-config-controll pull-right">
        <a href="#" class="btn btn-xs sx-move" title="Поменять порядок">
            <i class="glyphicon glyphicon-resize-vertical"></i>
        </a>
        
        <a href="#" class="btn btn-xs sx-remove" title="Удалить фильтр">
            <i class="glyphicon glyphicon-remove"></i>
        </a>
        
    </div>
</div>
HTML;

        $defaultOptions = [
            'class' => ActiveForm::class,
            'layout' => 'horizontal',
            'options' => [
                'class' => 'sx-backend-filters-form',
                'data-pjax' => 1
            ],
            'fieldConfig' => [
                'inputTemplate' => "{input}",
                'template' => "{label}\n{beginWrapper}\n<div class='sx-filter-wrapper'>{input}</div>\n{hint}\n{error}\n{endWrapper}{$fieldControlls}",
                'horizontalCssClasses' => [
                    'wrapper' => 'col-sm-7',
                ]
            ]
        ];

        $this->activeForm = ArrayHelper::merge($defaultOptions, $this->activeForm);

        parent::init();

        Html::addCssClass($this->wrapperOptions, 'sx-backend-filters-wrapper');
    }
}