<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets\forms;

use skeeks\cms\backend\forms\ActiveFormHasCustomSelectTrait;
use skeeks\cms\forms\ActiveFormHasFieldSetsTrait;
use skeeks\cms\forms\ActiveFormHasPjaxTrait;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class NumberInputWidget extends \skeeks\cms\base\InputWidget
{
    /**
     * @var string
     */
    static public $autoIdPrefix = "NumberInputWidget";

    /**
     * @var bool
     */
    public $append = false;

    /**
     * @var bool
     */
    public $prepend = false;

    /**
     * @var string
     */
    public $maxInputWidth = "200px";

    /**
     * @var string
     */
    public $viewFile = 'input';
}