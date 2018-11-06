<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets\filters;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class Bootstrap4ActiveField extends \yii\bootstrap4\ActiveField
{

    protected function createLayoutConfig($instanceConfig)
    {
        $config = parent::createLayoutConfig($instanceConfig);
        $config['template'] = "{label}\n{beginWrapper}\n<div class='sx-filter-wrapper'>{input}</div>\n{hint}\n{error}\n{endWrapper}{controlls}";
        $config['wrapperOptions'] = [
            'class' => 'col-sm-7',
        ];
        $config['labelOptions'] = [
            'class' => 'col-sm-3 col-form-label',
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
        $this->parts['{controlls}'] = <<<HTML
        <div class="col-sm-2 sx-field-controll">
            <div class="sx-field-config-controll pull-right">
                <a href="#" class="btn btn-xs sx-move" title="Поменять порядок">
                    <i class="fas fa-arrows-alt-v"></i>
                </a>
                <a href="#" class="btn btn-xs sx-remove" title="Удалить фильтр">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
HTML;

    }

}