<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */
namespace skeeks\cms\backend\events;

use yii\base\Event;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ViewRenderEvent extends Event {

    /**
     * @var string 
     */
    public $content = '';
}