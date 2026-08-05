<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\helpers\BackendIcon;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Declarative trigger for the shared backend quick-access runtime.
 */
class BackendQuickAccessFavoriteButton extends Widget
{
    /** @var array Serializable quick-access item. */
    public $item = [];

    /** @var array Button options. */
    public $options = [];

    public function init()
    {
        parent::init();

        foreach (['type', 'id', 'name', 'url', 'action'] as $key) {
            if (!array_key_exists($key, $this->item)) {
                throw new InvalidConfigException("Quick-access item key '{$key}' is required.");
            }
        }

        Html::addCssClass($this->options, 'sx-quick-access-favorite-btn');
        $this->options = array_merge([
            'type'                          => 'button',
            'data-sx-quick-access-favorite' => true,
            'data-sx-quick-access-item'     => Json::encode($this->item),
            'title'                         => \Yii::t('skeeks/backend', 'Add to favorites'),
            'aria-label'                    => \Yii::t('skeeks/backend', 'Add to favorites'),
            'aria-pressed'                  => 'false',
        ], $this->options);
    }

    public function run()
    {
        return Html::button(BackendIcon::render('star', ['size' => 17]), $this->options);
    }
}
