<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\actions;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use yii\widgets\ActiveForm;

/**
 * @property null|ActiveForm $activeForm read-only;
 * @property string $activeFormClassName;
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
    public $activeFormClassName = 'skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab';

    /**
     * @var null|ActiveForm|ActiveFormUseTab
     */
    protected $_activeForm = null;

    /**
     * @param array $config
     *
     * @return ActiveForm|ActiveFormUseTab
     */
    public function beginActiveForm(array $config = [])
    {
        $className = $this->activeFormClassName;
        $this->_activeForm = $className::begin($config);
        return $this->_activeForm;
    }

    /**
     * @param array $config
     *
     * @return mixed
     */
    public function endActiveForm(array $config = [])
    {
        $className = $this->activeFormClassName;
        return $className::end();
    }

    /**
     * @return null|ActiveFormUseTab|ActiveForm
     */
    public function getActiveForm()
    {
        return $this->_activeForm;
    }
}