<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use skeeks\cms\backend\controllers\BackendModelController;
use skeeks\cms\backend\widgets\ControllerActionsWidget;
use skeeks\cms\cmsWidgets\gridView\GridViewCmsWidget;
use skeeks\cms\widgets\DynamicFiltersWidget;
use skeeks\cms\widgets\FiltersWidget;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class BackendGridModelRelatedAction extends BackendModelAction
{
    const EVENT_GRID_INIT = 'gridInit';
    /**
     * @var
     */
    public $controllerRoute = '';
    public $actionName = 'index';

    /**
     * @var bool
     */
    public $isStandartBeforeRender = true;
    public $filters = true;
    public $backendShowings = true;
    public $relation = [];

    public function init()
    {
        if (!$this->icon) {
            $this->icon = "fa fa-list";
        }

        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', "List");
        }

        parent::init();
    }

    /**
     * @var null|BackendGridModelAction
     */
    public $relatedIndexAction = null;

    /**
     * @param $model
     * @return array
     */
    public function getBindRelation($model)
    {
        $result = [];
        if ($this->relation) {
            foreach ($this->relation as $key => $value) {
                $result[$key] = $model->{$value};
            }
        }

        return $result;
    }

    public $relatedController = null;

    public function run()
    {
        if ($controller = \Yii::$app->createController($this->controllerRoute)) {
            /**
             * @var $controller BackendController
             * @var $indexAction BackendGridModelAction
             */
            $controller = $controller[0];
            $this->relatedController = $controller;

            $controller->actionsMap = [
                'index' => [
                    'configKey'         => $this->uniqueId,
                    'backendShowingKey' => $this->uniqueId,
                    'url'               => $this->urlData,
                ],
            ];


            if ($indexAction = ArrayHelper::getValue($controller->actions, $this->actionName)) {
                $this->relatedIndexAction = $indexAction;

                $this->relatedIndexAction->url = $this->urlData;

                if ($this->filters === false) {
                    $this->relatedIndexAction->filters = $this->filters;
                }


                if ($this->backendShowings === false) {
                    $this->relatedIndexAction->backendShowings = $this->backendShowings;
                }


                /*$visibleColumns = $this->relatedIndexAction->grid['visibleColumns'];

                $this->relatedIndexAction->grid['visibleColumns'] = $visibleColumns;*/
                $this->relatedIndexAction->grid['columns']['actions']['isOpenNewWindow'] = true;

                if ($this->relation) {

                    $relation = $this->relation;

                    $baseOnInit = null;
                    if (isset($this->relatedIndexAction->grid['on init'])) {
                        $baseOnInit = $this->relatedIndexAction->grid['on init'];
                    }
                    $this->relatedIndexAction->grid['on init'] = function (Event $e) use ($relation, $baseOnInit) {
                        /**
                         * @var $query ActiveQuery
                         */
                        $query = $e->sender->dataProvider->query;

                        if ($baseOnInit) {
                            $baseOnInit($e);
                        }

                        $query->andWhere($this->getBindRelation($this->model));
                    };
                }

                $this->trigger(self::EVENT_GRID_INIT);

                if ($this->isStandartBeforeRender) {
                    $relation = $this->relation;

                    $this->relatedIndexAction->on('beforeRender', function (Event $event) use ($controller, $relation) {

                        if ($createAction = ArrayHelper::getValue($controller->actions, 'create')) {

                            /**
                             * @var $controller BackendModelController
                             * @var $createAction BackendModelCreateAction
                             */
                            $r = new \ReflectionClass($controller->modelClassName);

                            if ($relation) {
                                $createAction->url = ArrayHelper::merge($createAction->urlData, [
                                    $r->getShortName() => $this->getBindRelation($this->model),
                                ]);
                            }

                            //$createAction->name = "Добавить платеж";

                            $event->content = ControllerActionsWidget::widget([
                                    'actions'         => [$createAction],
                                    'isOpenNewWindow' => true,
                                    'minViewCount'    => 1,
                                    'itemTag'         => 'button',
                                    'itemOptions'     => ['class' => 'btn btn-primary'],
                                ])."<br>";
                        }
                    });
                }


                return $this->relatedIndexAction->run();
            }

        }

        return $this->controller->renderContent("Контроллер {$this->controllerRoute} не найден");
    }
}