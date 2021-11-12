<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\forms\ActiveFieldBackend;
use skeeks\cms\backend\forms\ActiveFormHasCustomSelectTrait;
use skeeks\cms\backend\forms\TActiveFormHasButtons;
use skeeks\cms\backend\forms\TActiveFormHasCustomSelect;
use skeeks\cms\backend\widgets\assets\BackendFormAsset;
use skeeks\cms\forms\ActiveFormHasFieldSetsTrait;
use skeeks\cms\forms\ActiveFormHasPjaxTrait;
use skeeks\cms\forms\IActiveFormHasFieldSets;
use skeeks\cms\forms\TActiveFormDynamicReload;
use skeeks\cms\forms\TActiveFormHasFieldSets;
use skeeks\cms\forms\TActiveFormHasPjax;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ActiveFormBackend extends ActiveForm implements IActiveFormHasFieldSets
{
    use TActiveFormHasFieldSets;
    use TActiveFormHasButtons;
    use TActiveFormHasCustomSelect;
    use TActiveFormHasPjax;
    use TActiveFormDynamicReload;

    public static $autoIdPrefix = "bf";
    
    /**
     * @var string
     */
    public $fieldClass = ActiveFieldBackend::class;

    /**
     * @var bool Подключить стандартные js и css?
     */
    public $registerStandartAsset = true;

    /**
     * @var bool 
     */
    public $enableClientValidation = false;

    public function init()
    {
        Html::addCssClass($this->options, "sx-backend-form");
        Html::addCssClass($this->options, "sx-backend-form-inline");

        if ($this->registerStandartAsset) {
            BackendFormAsset::register($this->view);
        }

        $this->_initPjax()
            ->_initDynamicReload()
        ;

        parent::init();

    }


}