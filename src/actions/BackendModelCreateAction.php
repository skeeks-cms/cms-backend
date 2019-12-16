<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\controllers\IBackendModelController;
use skeeks\cms\backend\ViewBackendAction;
use skeeks\cms\helpers\RequestResponse;
use skeeks\cms\IHasUrl;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @property IBackendModelController|IHasUrl $controller
 *
 * Class BackendModelCreateAction
 * @package skeeks\cms\backend\actions
 */
class BackendModelCreateAction extends ViewBackendAction
    implements IHasActiveForm
{
    use THasActiveForm;
    use TBackendModelUpdateAction;

    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_VALIDATE = 'beforeValidate';

    public $isOpenNewWindow = true;

    public $defaultView = "_form";

    public function init()
    {
        if (!$this->icon) {
            $this->icon = "fa fa-plus";
        }


        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', "Add");
        }

        parent::init();
    }


    public $model;

    const EVENT_INIT_FORM_MODELS = 'initFormModels';

    public function run()
    {
        $is_saved = false;
        $redirect = "";

        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        $modelClassName = $this->controller->modelClassName;
        $model = new $modelClassName();

        $this->model = $model;

        $model->loadDefaultValues();

        $this->formModels['model'] = $model;
        $this->trigger(self::EVENT_INIT_FORM_MODELS);

        $rr = new RequestResponse();

        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            foreach ($this->formModels as $model) {
                $model->load(\Yii::$app->request->post());
            }
            return ActiveForm::validateMultiple($this->formModels);
        }

        if ($post = \Yii::$app->request->post()) {
            foreach ($this->formModels as $fmodel) {
                $fmodel->load(\Yii::$app->request->post());
            }
        }

        $isValid = true;


        if ($this->fields) {
            if (is_callable($this->fields)) {
                $fields = $this->fields;
                $this->fields = call_user_func($fields, $this);
            }
        }

        if ($rr->isRequestPjaxPost()) {

            try {
                if (!\Yii::$app->request->post($this->reloadFormParam)) {

                    foreach ($this->formModels as $fmodel) {
                        $fmodel->load(\Yii::$app->request->post());
                    }


                    $this->trigger(self::EVENT_BEFORE_VALIDATE);

                    foreach ($this->formModels as $fmodel) {
                        if ($fmodel->validate()) {

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

                        $this->trigger(self::EVENT_AFTER_SAVE);

                        if (!$this->successMessage) {
                            $this->successMessage = \Yii::t('skeeks/cms', 'Saved');
                        }

                        \Yii::$app->getSession()->setFlash('success', $this->successMessage);
                        $is_saved = true;

                        if (\Yii::$app->request->post('submit-btn') == 'apply') {
                            $url = '';
                            $this->controller->model = $model;

                            if ($this->controller->modelActions) {
                                if ($action = ArrayHelper::getValue($this->controller->modelActions,
                                    $this->controller->modelDefaultAction)) {
                                    $url = $action->url;
                                }
                            }

                            if (!$url) {
                                $url = $this->controller->url;
                            }

                            $redirect = $url;
                        } else {

                            if (!$this->afterSaveUrl) {
                                $this->afterSaveUrl = $this->controller->url;
                            }

                            $redirect = $this->afterSaveUrl;
                        }

                        if (is_array($redirect)) {
                            $redirect = Url::to($redirect);
                        }

                    } else {
                        /*$formModels = $this->formModels;

                        print_R($formModels);die;*/
                    }

                }
            } catch (\Exception $e) {
                if (YII_ENV_DEV) {
                    throw $e;
                } else {
                    \Yii::$app->getSession()->setFlash('error', $e->getMessage());
                }
                //\Yii::$app->getSession()->setFlash('error', $e->getMessage());
            }
        }

        if ($this->fields) {

            return $this->render('@skeeks/cms/backend/actions/views/model-update', [
                'model'      => $model,
                'formModels' => $this->formModels,
                'is_saved'   => $is_saved,
                'redirect'   => $redirect,
                'is_create'  => true,
                'submitBtn'  => \Yii::$app->request->post('submit-btn'),
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