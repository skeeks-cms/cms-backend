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
            $this->icon = "glyphicon glyphicon-pencil";
        }

        if (!$this->priority) {
            $this->priority = 10;
        }

        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', "Edit");
        }

        parent::init();
    }

    public $formModels = [];

    const EVENT_INIT_FORM_MODELS = 'initFormModels';

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

        if ($rr->isRequestPjaxPost()) {

            try {
                if (!\Yii::$app->request->post($this->reloadFormParam)) {
                    foreach ($this->formModels as $model) {
                        $model->load(\Yii::$app->request->post());
                        
                    }

                    foreach ($this->formModels as $model) {
                        if ($model->validate()) {
                            
                        } else {
                            throw new Exception("Не удалось сохранить данные: " . print_r($model->errors, true));
                        }
                    }
                    
                    foreach ($this->formModels as $model) {
                        if ($model->save($this->modelValidate)) {
                            $model->refresh();
                        } else {
                            throw new Exception("Не удалось сохранить данные: " . print_r($model->errors, true));
                        }
                    }

                    \Yii::$app->getSession()->setFlash('success', \Yii::t('skeeks/cms', 'Saved'));

                    if (\Yii::$app->request->post('submit-btn') == 'apply') {

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
                'model' => $this->model,
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