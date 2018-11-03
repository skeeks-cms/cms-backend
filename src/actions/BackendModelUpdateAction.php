<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\IHasModel;
use skeeks\cms\IHasUrl;
use yii\base\DynamicModel;
use yii\base\Exception;
use yii\widgets\ActiveForm;

/**
 * @property IBackendModelController|IHasUrl|IHasModel $controller
 *
 * Class BackendModelCreateAction
 * @package skeeks\cms\backend\actions
 */
class BackendModelUpdateAction extends BackendModelAction
    implements IHasActiveForm
{
    use THasActiveForm;

    const EVENT_INIT_FORM_MODELS = 'initFormModels';
    const EVENT_BEFORE_SAVE = 'beforeSave';
    /**
     * @var bool
     */
    public $modelValidate = true;
    /**
     * @var string
     */
    public $defaultView = "_form";
    /**
     * @var bool
     */
    public $isSaveFormModels = true;

    /**
     * @var string
     */
    public $afterContent = '';

    /**
     * @var string
     */
    public $beforeContent = '';

    /**
     * @var string
     */
    public $afterSaveUrl = '';

    /**
     * @var string
     */
    public $successMessage = '';

    /**
     * @var array|callable
     */
    public $fields = [];
    
    public $formModels = [];
    
    public function init()
    {
        if (!$this->icon) {
            $this->icon = "fa fa-edit";
        }

        if (!$this->priority) {
            $this->priority = 10;
        }

        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', "Edit");
        }

        parent::init();
    }
    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        $this->formModels['model'] = $this->model;
        $this->trigger(self::EVENT_INIT_FORM_MODELS);

        $rr = new RequestResponse();

        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            foreach ($this->formModels as $model) {
                $model->load(\Yii::$app->request->post());
            }
            return ActiveForm::validateMultiple($this->formModels);
        }

        if ($post = \Yii::$app->request->post()) {
            foreach ($this->formModels as $model) {
                $model->load(\Yii::$app->request->post());
            }
        }

        $isValid = true;

        if ($rr->isRequestPjaxPost()) {

            try {
                if (!\Yii::$app->request->post($this->reloadFormParam)) {

                    foreach ($this->formModels as $model) {
                        $model->load(\Yii::$app->request->post());
                    }

                    /**
                     * @var $model DynamicModel
                     */
                    foreach ($this->formModels as $model) {
                        if ($model->validate()) {

                        } else {
                            $isValid = false;
                            //throw new Exception("Не удалось сохранить данные: ".print_r($model->errors, true));
                        }
                    }

                    if ($isValid) {
                        $this->trigger(self::EVENT_BEFORE_SAVE);

                        if ($this->isSaveFormModels) {
                            foreach ($this->formModels as $model) {
                                if (method_exists($model, 'save') && $model->save($this->modelValidate)) {
                                    $model->refresh();
                                } else {
                                    throw new Exception("Не удалось сохранить данные: ".print_r($model->errors, true));
                                }
                            }
                        }

                        if (!$this->successMessage) {
                            $this->successMessage = \Yii::t('skeeks/cms', 'Saved');
                        }

                        \Yii::$app->getSession()->setFlash('success', $this->successMessage);

                        if (\Yii::$app->request->post('submit-btn') == 'apply') {

                        } else {
                            if (!$this->afterSaveUrl) {
                                $this->afterSaveUrl = $this->controller->url;
                            }

                            return $this->controller->redirect(
                                $this->afterSaveUrl
                            );
                        }
                    }

                } else {
                    foreach ($this->formModels as $model) {
                        $model->load(\Yii::$app->request->post());
                        $model->validate();
                    }
                }
            } catch (\Exception $e) {
                \Yii::$app->getSession()->setFlash('error', $e->getMessage());
            }
        }

        if ($this->fields) {
            if (is_callable($this->fields)) {
                $fields = $this->fields;
                $this->fields = call_user_func($fields, $this);
            }

            return $this->render('@skeeks/cms/backend/actions/views/model-update', [
                'model'      => $this->model,
                'formModels' => $this->formModels,
            ]);
            //return $this->render('@skeeks/cms/backend/actions/views/model-update');
        }

        return parent::run();
    }

    /**
     * Renders a view
     *
     * @param string $viewName view name
     * @return string result of the rendering
     */
    protected function render($viewName, $params = [])
    {
        $params['model'] = $this->model;
        return parent::render($viewName, $params);
    }
}