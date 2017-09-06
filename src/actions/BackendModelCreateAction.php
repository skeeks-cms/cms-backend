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
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

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
    public $modelScenario = "";

    /**
     * @var string
     */
    public $defaultView = "_form";
    
    public function init()
    {
        if (!$this->icon)
        {
            $this->icon = "glyphicon glyphicon-plus";
        }


        if (!$this->name)
        {
            $this->name = \Yii::t('skeeks/backend', "Add");
        }

        parent::init();
    }

    /**
     * @return $this|array|mixed
     */
    public function run()
    {
        if ($this->callback)
        {
            return call_user_func($this->callback, $this);
        }

        $modelClassName = $this->controller->modelClassName;
        $model          = new $modelClassName();
        $scenarios      = $model->scenarios();

        if ($scenarios && $this->modelScenario)
        {
            if (isset($scenarios[$this->modelScenario]))
            {
                $model->scenario = $this->modelScenario;
            }
        }

        $model->loadDefaultValues();

        $rr = new RequestResponse();

        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax)
        {
            return $rr->ajaxValidateForm($model);
        }

        if ($rr->isRequestPjaxPost())
        {
            if ($model->load(\Yii::$app->request->post()) && $model->save($this->modelValidate))
            {
                \Yii::$app->getSession()->setFlash('success', \Yii::t('skeeks/cms','Saved'));

                if (\Yii::$app->request->post('submit-btn') == 'apply')
                {
                    $url = '';
                    $this->controller->model = $model;

                    if ($this->controller->modelActions)
                    {
                        if ($action = ArrayHelper::getValue($this->controller->modelActions, $this->controller->modelDefaultAction))
                        {
                            $url = $action->url;
                        }
                    }

                    if (!$url)
                    {
                        $url = $this->controller->url;
                    }

                    return $this->controller->redirect($url);
                } else
                {
                    return $this->controller->redirect(
                        $this->controller->url
                    );
                }

            } else
            {
                \Yii::$app->getSession()->setFlash('error', \Yii::t('skeeks/cms','Could not save'));
            }
        }

        $this->controller->model = $model;

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