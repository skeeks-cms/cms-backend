<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\forms;

use yii\widgets\ActiveField;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ActiveFieldBackend extends ActiveField
{
    const INLINE_TEMPLATE = '<div class="row sx-inline-row"><div class="col-md-3 text-md-right my-auto">{label}</div><div class="col-md-9">{input}{hint}{error}</div></div>';

    public $template = self::INLINE_TEMPLATE;
    
}