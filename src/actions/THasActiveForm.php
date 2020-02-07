<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 05.03.2017
 */
namespace skeeks\cms\backend\actions;
use skeeks\cms\backend\widgets\ActiveFormBackend;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use yii\helpers\Json;
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
    //public $activeFormClassName = 'skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab';
    public $activeFormClassName = ActiveFormBackend::class;

    /**
     * @var null|ActiveForm|ActiveFormUseTab
     */
    protected $_activeForm = null;

    /**
     * @deprecated
     *
     * @var string
     */
    public $reloadFormParam = 'sx-reload-form';

    /**
     * @deprecated
     *
     * @var string
     */
    public $reloadFieldParam = 'data-form-reload';

    /**
     * @var array
     */
    public $buttons = ['apply', 'save', 'close'];

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
     * @deprecated 
     * 
     * @param array $config
     * @return ActiveFormUseTab|ActiveForm
     */
    /*public function beginDynamicActiveForm(array $config = [])
    {
        $form = $this->beginActiveForm([
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            //'dynamicReloadNotSubmit' => $this->reloadFormParam,
            //'dynamicReloadFieldParam' => $this->reloadFieldParam,
        ]);

        return $form;
    }*/

    /**
     * @param array $config
     *
     * @return mixed
     */
    /*public function endActiveForm(array $config = [])
    {
        $className = $this->activeFormClassName;
        return $className::end();
    }*/

    /**
     * @return null|ActiveFormUseTab|ActiveForm
     */
    public function getActiveForm()
    {
        return $this->_activeForm;
    }
}