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
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
    use TBackendModelUpdateAction;

    const EVENT_INIT_FORM_MODELS = 'initFormModels';
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_VALIDATE = 'beforeValidate';

    public $defaultView = "_form";

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

        if (!$this->successMessage) {
            $this->successMessage = "✓ " . \Yii::t('skeeks/cms', 'Saved');
        }

        parent::init();
    }


    public function run()
    {
        $is_saved = false;
        $redirect = "";

        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        $this->formModels['model'] = $this->model;
        $this->trigger(self::EVENT_INIT_FORM_MODELS);

        $rr = new RequestResponse();

        if ($post = \Yii::$app->request->post()) {
            foreach ($this->formModels as $model) {
                $model->load(\Yii::$app->request->post());
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
                if (!\Yii::$app->request->post(RequestResponse::DYNAMIC_RELOAD_NOT_SUBMIT)) {

                    foreach ($this->formModels as $model) {
                        $model->load(\Yii::$app->request->post());
                    }
                    $this->trigger(self::EVENT_BEFORE_VALIDATE);

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

                        $this->trigger(self::EVENT_AFTER_SAVE);

                        $is_saved = true;
                        \Yii::$app->getSession()->setFlash('success', $this->successMessage);

                        if (\Yii::$app->request->post('submit-btn') == 'apply') {

                        } else {
                            if (!$this->afterSaveUrl) {
                                $this->afterSaveUrl = $this->controller->url;
                            }

                            $redirect = $this->afterSaveUrl;
                            if (is_array($redirect)) {
                                $redirect = Url::to($redirect);
                            }
                        }
                    }

                } else {
                    /*foreach ($this->formModels as $model) {
                        $model->load(\Yii::$app->request->post());
                        $model->validate();
                    }*/
                }
            } catch (\Exception $e) {
                \Yii::$app->getSession()->setFlash('error', $e->getMessage());
            }
        }


        //Новые формы отправляемые через Ajax
        else if ($rr->isRequestAjaxPost()) {
            

            //Загрузка данных в каждую модель
            foreach ($this->formModels as $fmodel) {
                $fmodel->load(\Yii::$app->request->post());
            }

            //Событие перед валидацией (в контроллере можно перехватить и что то еще сделать)
            $this->trigger(self::EVENT_BEFORE_VALIDATE);

            foreach ($this->formModels as $fmodel) {
                if ($fmodel->validate()) {

                } else {
                    $isValid = false;
                    //throw new Exception("Не удалось сохранить данные: ".print_r($model->errors, true));
                }
            }


            if ($isValid) {

                //Если данные корректны и загружены в модели, очередное событеие что готовы сохранять эти данные.
                $this->trigger(self::EVENT_BEFORE_SAVE);

                //Сохранение данных
                if ($this->isSaveFormModels) {
                    foreach ($this->formModels as $model) {
                        if (method_exists($model, 'save')) {
                            if ($model->save($this->modelValidate)) {
                                $model->refresh();
                            } else {
                                throw new Exception("Не удалось сохранить данные: " . print_r($model->errors, true));
                            }
                        }
                    }
                }

                //Событие после сохранения(в контроллере можно перехватить и что то еще сделать)
                $this->trigger(self::EVENT_AFTER_SAVE);

                if (!$this->afterSaveUrl) {
                    $this->afterSaveUrl = $this->controller->url;
                }

                $rr->data = [
                    'afterSaveUrl' => $this->afterSaveUrl,
                    'type' => 'update',
                    'submitBtn' => \Yii::$app->request->post('submit-btn')
                ];

                $rr->message = $this->successMessage;
                $rr->success = true;

            } else {
                //TODO:доработать
                $validatingResult = [];
                foreach ($this->formModels as $model)
                {
                    $validatingResult = ArrayHelper::merge($validatingResult, ActiveForm::validate($model));
                }
                $rr->data = [
                    'validation' => $validatingResult
                ];
                $rr->success = false;
            }

            return $rr;
        }

        if ($this->fields) {

            return $this->render('@skeeks/cms/backend/actions/views/model-update', [
                'model'      => $this->model,
                'formModels' => $this->formModels,
                'is_saved'   => $is_saved,
                'submitBtn'  => \Yii::$app->request->post('submit-btn'),
                'redirect'   => $redirect,
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