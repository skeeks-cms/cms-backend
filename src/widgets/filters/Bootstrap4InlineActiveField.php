<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets\filters;

use skeeks\cms\backend\helpers\BackendIcon;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class Bootstrap4InlineActiveField extends \yii\bootstrap4\ActiveField
{

    protected function createLayoutConfig($instanceConfig)
    {
        $config = parent::createLayoutConfig($instanceConfig);
        $config['template'] = "{label}\n{beginWrapper}\n<div class='sx-filter-wrapper'>{input}{controlls}</div>\n{hint}\n{error}\n{endWrapper}";
        $config['checkTemplate'] = "{label}\n{beginWrapper}\n<div class='sx-filter-wrapper'>{input}{controlls}</div>\n{hint}\n{error}\n{endWrapper}";
        $config['wrapperOptions'] = [
            'class' => 'col-sm-12',
        ];
        $config['labelOptions'] = [
            'class' => 'col-sm-12 col-form-label',
        ];
        return $config;
    }

    public $inputTemplate = '{input}';

    /**
     * @var bool
     */
    public $enableControlls = true;

    /**
     * @param null $content
     * @return string
     */
    public function render($content = null)
    {
        $attribute = $this->attribute;
        if ($pos = strpos($attribute, "[")) {
            $attribute = substr($attribute, 0, $pos);
        }
        $this->options['data-attribute'] = $attribute;

        if ($content === null) {
            if ($this->enableControlls === true) {
                $this->renderControllsParts();
            } else {
                $this->parts['{controlls}'] = '';
            }
        }

        return parent::render($content);
    }

    protected function renderControllsParts()
    {
        $moveIcon = BackendIcon::render('move-vertical', ['size' => 16]);
        $removeIcon = BackendIcon::render('close', ['size' => 16]);

        $this->parts['{controlls}'] = <<<HTML
        <div class="sx-field-controll">
            <div class="sx-field-config-controll float-right">
                <a href="#" class="sx-move" data-toggle="tooltip" title="Поменять порядок">
                    {$moveIcon}
                </a>
                <a href="#" class="sx-remove" data-toggle="tooltip" title="Удалить фильтр">
                    {$removeIcon}
                </a>
            </div>
        </div>
HTML;

    }

}
