<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\forms\ActiveFormHasButtonsTrait;
use skeeks\cms\backend\forms\ActiveFormHasCustomSelectTrait;
use skeeks\cms\backend\widgets\assets\BackendFormAsset;
use skeeks\cms\forms\AdtiveFormHasFieldSetsTrait;
use skeeks\cms\forms\AdtiveFormHasPjaxTrait;
use skeeks\cms\forms\IActiveFormHasFieldSets;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ActiveFormBackend extends ActiveForm implements IActiveFormHasFieldSets
{
    use AdtiveFormHasFieldSetsTrait;
    use ActiveFormHasButtonsTrait;
    use ActiveFormHasCustomSelectTrait;
    use AdtiveFormHasPjaxTrait;

    /**
     * @var bool Подключить стандартные js и css?
     */
    public $registerStandartAsset = true;

    public function init()
    {
        Html::addCssClass($this->options, "sx-backend-form");

        if ($this->registerStandartAsset) {
            BackendFormAsset::register($this->view);
        }

        $this->_initPjax();

        parent::init();

    }


}