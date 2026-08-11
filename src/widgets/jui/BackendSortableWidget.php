<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets\jui;

use skeeks\cms\backend\widgets\jui\assets\BackendSortableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\Sortable;

/**
 * Legacy Sortable provider that loads only the jQuery UI modules required by
 * extensions which have not migrated to the backend SortableJS adapter.
 *
 * @deprecated Use BackendSortableAdapterAsset and sx.backend.sortable.create().
 */
class BackendSortableWidget extends Sortable
{
    public function run()
    {
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'ul');
        $result = Html::beginTag($tag, $options)."\n";
        $result .= $this->renderItems()."\n";
        $result .= Html::endTag($tag)."\n";

        BackendSortableAsset::register($this->getView());

        $id = $this->options['id'];
        $this->registerClientEvents('sortable', $id);
        $this->registerClientOptions('sortable', $id);

        return $result;
    }
}
