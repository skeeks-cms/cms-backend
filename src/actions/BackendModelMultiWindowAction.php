<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\actions\assets\BackendModelMultiWindowActionAsset;
use skeeks\cms\backend\helpers\BackendUrlHelper;
use skeeks\cms\helpers\RequestResponse;
use skeeks\sx\helpers\ResponseHelper;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/**
 * A parameterized multi-action rendered in a standard backend iframe window.
 *
 * Selected primary keys are posted once and stored in the server cache under
 * a user-bound key. The iframe URL contains only a short-lived opaque token,
 * so large selections never end up in the URL or in the form.
 */
class BackendModelMultiWindowAction extends BackendModelMultiAction implements IHasActiveForm
{
    use THasActiveForm;

    /** @var Model|array|string|callable */
    public $formModel;

    /** @var array|callable */
    public $fields = [];

    /** @var callable */
    public $applyCallback;

    /** @var callable|null Receives ActiveQuery and this action. */
    public $modelsQueryCallback;

    /** @var bool */
    public $useTransaction = true;

    /** @var int */
    public $selectionTtl = 900;

    /** @var int */
    public $maxSelection = 10000;

    /** @var string */
    public $selectionPrepareParam = '_sx_prepare_multi_window';

    /** @var string */
    public $selectionTokenParam = '_sx_multi_token';

    /** @var string */
    public $successMessage = '';

    /** @var string */
    public $beforeContent = '';

    /** @var string */
    public $afterContent = '';

    /** @var string */
    public $defaultView = '@skeeks/cms/backend/actions/views/model-update';

    /** @var Model */
    private $_formModel;

    public function init()
    {
        $this->isOpenNewWindow = true;
        if ($this->buttons === ['apply']) {
            $this->buttons = ['apply', 'save'];
        }

        if (!$this->successMessage) {
            $this->successMessage = \Yii::t('skeeks/cms', 'Saved');
        }

        parent::init();
    }

    public function run()
    {
        if (\Yii::$app->request->isPost && \Yii::$app->request->post($this->selectionPrepareParam)) {
            return $this->prepareSelection();
        }

        $selection = $this->getSelection();
        if (!$selection) {
            return $this->controller->renderContent(Html::tag(
                'div',
                \Yii::t('skeeks/cms', 'The selection has expired. Close this window and select the records again.'),
                ['class' => 'sx-surface sx-surface--padded']
            ));
        }

        $formModel = $this->getFormModel();
        if (is_callable($this->fields)) {
            $fields = $this->fields;
            $this->fields = call_user_func($fields, $this);
        }

        $rr = new RequestResponse();
        if ($rr->isRequestAjaxPost()) {
            $formModel->load(\Yii::$app->request->post());

            if (!$formModel->validate()) {
                $rr->data = ['validation' => ActiveForm::validate($formModel)];
                return $rr;
            }

            try {
                $updated = $this->applySelection($selection['pks'], $formModel);
                $rr->success = true;
                $rr->message = $this->successMessage . ' ' . \Yii::t('skeeks/cms', 'Updated records: {count}', [
                    'count' => $updated,
                ]);
                $rr->data = [
                    'type' => 'update',
                    'submitBtn' => \Yii::$app->request->post('submit-btn'),
                    'updated' => $updated,
                ];
                if (\Yii::$app->request->post('submit-btn') === 'save') {
                    \Yii::$app->cache->delete($this->getSelectionCacheKey($selection['token']));
                }
            } catch (\Throwable $e) {
                $rr->success = false;
                $rr->message = $e->getMessage();
            }

            return $rr;
        }

        return $this->controller->render($this->defaultView, [
            'model' => $formModel,
            'formModels' => ['model' => $formModel],
            'is_saved' => false,
            'submitBtn' => null,
            'redirect' => '',
        ]);
    }

    public function getFormModel()
    {
        if ($this->_formModel) {
            return $this->_formModel;
        }

        $model = $this->formModel;
        if (is_callable($model)) {
            $model = call_user_func($model, $this);
        }
        if (is_string($model) || is_array($model)) {
            $model = \Yii::createObject($model);
        }
        if (!$model instanceof Model) {
            throw new InvalidConfigException(static::class . '::formModel must resolve to ' . Model::class);
        }

        return $this->_formModel = $model;
    }

    protected function prepareSelection()
    {
        $rr = new ResponseHelper();
        $pks = (array)\Yii::$app->request->post($this->controller->requestPkParamName, []);
        $pks = array_values(array_unique(array_filter($pks, static function ($pk) {
            return is_scalar($pk) && (string)$pk !== '';
        })));

        if (!$pks) {
            $rr->success = false;
            $rr->message = \Yii::t('skeeks/cms', 'Select at least one record.');
            return (array)$rr;
        }
        if (count($pks) > $this->maxSelection) {
            $rr->success = false;
            $rr->message = \Yii::t('skeeks/cms', 'Too many records selected. Maximum: {count}.', [
                'count' => $this->maxSelection,
            ]);
            return (array)$rr;
        }

        $token = \Yii::$app->security->generateRandomString(32);
        $isStored = \Yii::$app->cache->set($this->getSelectionCacheKey($token), [
            'pks' => $pks,
        ], $this->selectionTtl);
        if (!$isStored) {
            $rr->success = false;
            $rr->message = \Yii::t('skeeks/cms', 'Failed to prepare the selected records. Try again.');
            return (array)$rr;
        }

        $requestContext = \Yii::$app->request->get();
        unset($requestContext[$this->selectionTokenParam]);

        $urlData = is_array($this->urlData) ? $this->urlData : [$this->urlData];
        $urlData = ArrayHelper::merge($urlData, $requestContext);
        $urlData[$this->selectionTokenParam] = $token;
        $url = BackendUrlHelper::createByParams($urlData)
            ->enableEmptyLayout()
            ->enableNoActions()
            ->url;

        $rr->success = true;
        $rr->message = \Yii::t('skeeks/cms', 'Selected records: {count}', ['count' => count($pks)]);
        $rr->data = [
            'url' => $url,
            'count' => count($pks),
        ];

        return (array)$rr;
    }

    protected function getSelection()
    {
        $token = (string)\Yii::$app->request->get($this->selectionTokenParam);
        if (!$token) {
            return null;
        }

        $selection = \Yii::$app->cache->get($this->getSelectionCacheKey($token));
        if ($selection === false || !$selection) {
            return null;
        }

        $selection['token'] = $token;
        return $selection;
    }

    protected function getSelectionCacheKey($token)
    {
        $userId = \Yii::$app->user->isGuest ? 'guest' : (string)\Yii::$app->user->id;
        return '__skeeks_multi_window:' . hash('sha256', $userId . ':' . $this->uniqueId . ':' . $token);
    }

    protected function applySelection(array $pks, Model $formModel)
    {
        if (!$this->applyCallback || !is_callable($this->applyCallback)) {
            throw new InvalidConfigException(static::class . '::applyCallback must be callable');
        }

        $modelClass = $this->controller->modelClassName;
        /** @var ActiveQuery $query */
        $query = $modelClass::find()->andWhere([$this->controller->modelPkAttribute => $pks]);
        if ($this->modelsQueryCallback && is_callable($this->modelsQueryCallback)) {
            $queryCallback = $this->modelsQueryCallback;
            $result = call_user_func($queryCallback, $query, $this);
            if ($result instanceof ActiveQuery) {
                $query = $result;
            }
        }

        $this->models = $query->all();
        if (count($this->models) !== count($pks)) {
            throw new Exception(\Yii::t('skeeks/cms', 'Some selected records are unavailable. Refresh the list and try again.'));
        }

        foreach ($this->models as $model) {
            if ($this->eachAccessCallback && !call_user_func($this->eachAccessCallback, $model)) {
                throw new Exception(\Yii::t('skeeks/cms', 'Access denied.'));
            }
        }

        $execute = function () use ($formModel) {
            $updated = 0;
            foreach ($this->models as $model) {
                if (call_user_func($this->applyCallback, $model, $formModel, $this) !== true) {
                    throw new Exception(\Yii::t('skeeks/cms', 'Failed to update record #{id}.', [
                        'id' => $model->{$this->controller->modelPkAttribute},
                    ]));
                }
                $updated++;
            }
            return $updated;
        };

        if ($this->useTransaction) {
            return \Yii::$app->db->transaction($execute);
        }

        return $execute();
    }

    public function registerForGrid($grid)
    {
        BackendModelMultiWindowActionAsset::register($grid->view);

        $clientOptions = Json::encode(array_merge($this->getClientOptions(), [
            'selectionPrepareParam' => $this->selectionPrepareParam,
            'size' => $this->size,
        ]));

        $grid->view->registerJs(<<<JS
(function(sx, $, _)
{
    new sx.classes.grid.MultiWindowAction(sx.Grid{$grid->id}, '{$this->id}', {$clientOptions});
})(sx, sx.$, sx._);
JS
        );

        return '';
    }
}
