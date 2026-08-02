<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendShellAsset;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Shared semantic footer frame with product-provided content.
 */
class BackendShellFooterWidget extends Widget
{
    /** @var string */
    public $content = '';

    /** @var array */
    public $options = [];

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = 'footer';
        }

        Html::addCssClass($this->options, 'sx-shell-footer');
    }

    /**
     * @return string
     */
    public function run()
    {
        BackendShellAsset::register($this->view);

        return $this->render('backend-shell-footer', [
            'content' => $this->content,
            'options' => $this->options,
        ]);
    }
}
