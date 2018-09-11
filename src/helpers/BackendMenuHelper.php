<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 09.03.2017
 */
namespace skeeks\cms\backend\helpers;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * @property array $navData
 *
 * Class BackendMenuHelper
 *
 * @package skeeks\cms\backend\helpers
 */
class BackendMenuHelper extends Component
{
    public $menu = null;

    public function init()
    {
        parent::init();

        if (!$this->menu)
        {
            throw new InvalidConfigException;
        }
    }

    /**
     * Data to build the widget \yii\bootstrap\Nav
     *
     * @return array
     */
    public function getNavData()
    {
        $result = [];

        if ($this->menu->items)
        {
            foreach ($this->menu->items as $item)
            {
                $data = $this->getRecursiveData($item);
                if (!ArrayHelper::getValue($data, 'url') && ! ArrayHelper::getValue($data, 'items')) {

                } else {
                    $result[$item->id] = $data;
                }
            }
        }

        return $result;
    }

    protected function getRecursiveData($item) {

        $result = [];

        $result['label'] = $item->name;

        if ($item->url) {
            $result['url'] = $item->url;
        }

        if ($item->items) {
            $items = [];
            foreach ($item->items as $childItem)
            {
                $result['items'][] = $this->getRecursiveData($childItem);
            }
        }

        return $result;
    }
}