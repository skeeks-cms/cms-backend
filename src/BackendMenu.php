<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 08.03.2017
 */
namespace skeeks\cms\backend;

use skeeks\cms\IHasInfo;
use skeeks\cms\IHasUrl;
use skeeks\cms\traits\THasInfo;
use skeeks\cms\traits\THasUrl;
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
    public $data            = [];

    /**
     * @var string
     */
    public $itemClass       = 'skeeks\cms\backend\BackendMenuItem';

    public function init()
    {
        parent::init();
    }

    /**
     * @var BackendMenuItem[]
     */
    protected $_items   = null;

    /**
     * @return BackendMenuItem[]
     */
    public function getItems()
    {
        if ($this->data && $this->_items === null)
        {
            $this->_items = $this->buid($this->data);
            ArrayHelper::multisort($this->_items, 'priority');
        }
        return $this->_items;
    }


    /**
     * @param array $data
     * @param bool|false $parent
     * @return array
     */
    public function buid($data = [], $parent = false)
    {
        $result         = [];
        $itemClass      = $this->itemClass;

        foreach ($data as $key => $value)
        {
            if ($value instanceof $itemClass)
            {
                $result[] = $value;
            }
            else if (is_callable($value))
            {
                $data = $value();
                if (is_array($data))
                {
                    $result = ArrayHelper::merge($result, $data);
                }

            }
            else if (is_array($value))
            {
                $itemData = (array) $value;

                if (!$itemData)
                {
                    continue;
                }

                if (is_string($key))
                {
                    $itemData['id'] = $key;
                } else
                {
                    if ($parent === null)
                    {
                        $itemData['id'] = 'sx-auto-block-' . $key;
                    } else
                    {
                        $itemData['id'] = $parent->id . '-' . $key;
                    }
                }

                if ($parent)
                {
                    $itemData['parent'] = $parent;
                }

                $itemData['menu'] = $this;
                $item = new $itemClass($itemData);

                if ($item->isAllow)
                {
                    if ($item->items)
                    {
                        $item->items = static::buid($item->items, $item);
                    }

                    if ($item->isAllow)
                    {
                        $result[] = $item;
                    }
                }

            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getDataBootstrapMenu()
    {
        $result = [];

        if ($this->items)
        {
            foreach ($this->items as $item)
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
                            'url' => $childItem->url,
                        ];
                    }

                    $result[$item->id]['items'] = $childItems;
                }
            }
        }

        return $result;
    }
}