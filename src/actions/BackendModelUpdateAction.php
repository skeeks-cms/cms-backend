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
    public $modelScenario = "";

    /**
     * @var string
     */
    public $defaultView = "_form";

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

    public function run()
    {
        if ($this->callback) {
            return call_user_func($this->callback, $this);
        }

        $model = $this->controller->model;

        $scenarios = [];
        if (method_exists($model, 'scenarios')) {
            $scenarios = $model->scenarios();
        }

        if ($scenarios && $this->modelScenario) {
            if (isset($scenarios[$this->modelScenario])) {
                $model->scenario = $this->modelScenario;
            }
        }

        $rr = new RequestResponse();

        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            return $rr->ajaxValidateForm($model);
        }

        if ($post = \Yii::$app->request->post()) {
            $model->load(\Yii::$app->request->post());
        }

        if ($rr->isRequestPjaxPost()) {
            if (!\Yii::$app->request->post($this->reloadFormParam)) {
                if ($model->load(\Yii::$app->request->post()) && $model->save($this->modelValidate)) {
                    \Yii::$app->getSession()->setFlash('success', \Yii::t('skeeks/cms', 'Saved'));

                    if (\Yii::$app->request->post('submit-btn') == 'apply') {

                    } else {
                        return $this->controller->redirect(
                            $this->controller->url
                        );
                    }

                    $model->refresh();
                } else {
                }
            }
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
        return $this->controller->render($viewName, ['model' => $this->controller->model]);
    }
}