<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\BackendAction;
use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\yii2\contextmenu\JqueryContextMenuWidget;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ContextMenuWidget extends JqueryContextMenuWidget
{
    public function run()
    {
        $this->view->registerJs(<<<JS
        
        jQuery.contextMenu.types.backendAction = function(item, opt, root) {
            this.addClass('sx-backend-action-wrapper');
            var aOptions = {
                'href' : '#',
            };
            if (item.onclick) {
                aOptions.onclick = item.onclick;
            }
            jQuery('<span>', aOptions).append(item.name)
                .appendTo(this);
        };
JS
        );

        if ($this->items) {
            foreach ($this->items as $key => $item)
            {
                if (!isset($item['type'])) {
                    $item['type'] = 'backendAction';
                }
                $this->items[$key] = $item;
            }
        }

        return parent::run();
    }
}
