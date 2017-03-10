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
                $result[$item->id] = [
                    'label' => $item->name,
                    'url' => $item->url,
                ];

                if ($item->items)
                {
                    $childItems = [];

                    foreach ($item->items as $childItem)
                    {
                        $childItems[] = [
                            'label' => $childItem->name,
                            'url'   => $childItem->url,
                        ];
                    }

                    $result[$item->id]['items'] = $childItems;
                }
            }
        }

        return $result;
    }
}