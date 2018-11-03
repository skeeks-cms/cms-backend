<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */

namespace skeeks\cms\backend;

use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * @property BackendMenuItem[] $items
 *
 * Class BackendMenu
 * @package skeeks\cms\backend
 */
class BackendMenu extends Component
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * @var string
     */
    public $itemClass = 'skeeks\cms\backend\BackendMenuItem';
    /**
     * @var BackendMenuItem[]
     */
    protected $_items = null;
    /**
     * @param array $config
     * @return array
     */
    static public function loadConfig(array $config = [])
    {
        $config = [];

        foreach ($config as $key => $itemData) {
            if ($items = ArrayHelper::getValue($itemData, 'items')) {
                if (is_callable($items)) {
                    $items = (array)call_user_func($items);
                }

                if ($items) {
                    foreach ($items as $subKey => $subItemData) {
                        $items[$subKey] = static::loadConfig($subItemData);
                    }
                }

                $config[$key]['items'] = $items;
            }
        }

        return $config;
    }
    public function init()
    {
        parent::init();
    }
    /**
     * @return BackendMenuItem[]
     */
    public function getItems()
    {
        if ($this->data && $this->_items === null) {
            $this->_items = $this->buid($this->data);
        }
        return $this->_items;
    }
    /**
     * @param array      $data
     * @param bool|false $parent
     * @return array
     */
    public function buid($data = [], $parent = false)
    {
        $result = [];
        $itemClass = $this->itemClass;

        foreach ($data as $key => $itemData) {
            if (is_array($itemData)) {
                if (!$itemData) {
                    continue;
                }

                if (is_string($key)) {
                    $itemData['id'] = $key;
                } else {
                    if ($parent === null) {
                        $itemData['id'] = 'sx-auto-block-'.$key;
                    } else {
                        $itemData['id'] = $parent->id.'-'.$key;
                    }
                }

                if ($parent) {
                    $itemData['parent'] = $parent;
                }

                $itemData['menu'] = $this;
                $itemData['class'] = $itemClass;
                $item = \Yii::createObject($itemData);

                if ($item->isAllow) {
                    $result[] = $item;

                    if ($itemsData = ArrayHelper::getValue($itemData, 'items')) {
                        $item->items = $this->buid($itemsData, $item);
                    }
                }
            }
        }

        ArrayHelper::multisort($result, 'priority');

        return $result;
    }
}