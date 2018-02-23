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
use yii\helpers\ArrayHelper;
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

    /**
     * @var bool
     */
    public $modelValidate = true;

    /**
     * @var string
     */
    public $defaultView = "_form";

    /**
     * @var array
     */
    public $fields = [];

    public function init()
    {
        if (!$this->icon) {
            $this->icon = "glyphicon glyphicon-plus";
        }


        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', "Add");
        }

        parent::init();
    }

    public $formModels = [];
    public $model;

    const EVENT_INIT_FORM_MODELS = 'initFormModels';

    public function run()
    {
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

        if ($rr->isRequestPjaxPost()) {

            try {
                if (!\Yii::$app->request->post($this->reloadFormParam)) {
                    foreach ($this->formModels as $fmodel) {
                        $fmodel->load(\Yii::$app->request->post());
                    }

                    foreach ($this->formModels as $fmodel) {
                        if ($fmodel->validate()) {

                        } else {
                            throw new Exception("Не удалось сохранить данные: " . print_r($fmodel->errors, true));
                        }
                    }

                    foreach ($this->formModels as $fmodel) {
                        if ($fmodel->save($this->modelValidate)) {
                            $fmodel->refresh();
                        } else {
                            throw new Exception("Не удалось сохранить данные: " . print_r($fmodel->errors, true));
                        }
                    }

                    \Yii::$app->getSession()->setFlash('success', \Yii::t('skeeks/cms', 'Saved'));

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

                        return $this->controller->redirect($url);
                    } else {
                        return $this->controller->redirect(
                            $this->controller->url
                        );
                    }
                }
            } catch (\Exception $e) {
                //\Yii::$app->getSession()->setFlash('error', $e->getMessage());
            }
        }

        if ($this->fields) {
            return $this->controller->render('@skeeks/cms/backend/actions/views/model-update', [
                'model' => $model,
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
    protected function render($viewName)
    {
        return $this->controller->render($viewName, ['model' => $this->model]);
    }
}