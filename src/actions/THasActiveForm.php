<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\widgets\ActiveFormAjaxBackend;
use skeeks\cms\backend\widgets\ActiveFormBackend;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * @property null|ActiveForm $activeForm read-only;
 *
 * Interface IHasActiveForm
 *
 * @package skeeks\cms\backend\actions
 */
trait THasActiveForm
{
    /**
     * @var string
     */
    //public $activeFormClassName = 'skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab';
    //public $activeFormClass = ActiveFormBackend::class;
    public $activeFormClass = ActiveFormAjaxBackend::class;

    /**
     * @var array
     */
    public $activeFormConfig = [];

    /**
     * @deprecated
     *
     * @var array
     */
    public $buttons = ['apply'];

    /**
     * @param array $config
     *
     * @return ActiveForm|ActiveFormUseTab
     */
    public function beginActiveForm(array $config = [])
    {
        $className = $this->activeFormClass;
        $config = ArrayHelper::merge((array)$this->activeFormConfig, $config);
        return $className::begin($config);
    }

}