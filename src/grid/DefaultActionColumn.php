<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 11.03.2018
 */

namespace skeeks\cms\backend\grid;

use yii\web\Controller;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class DefaultActionColumn extends BackendEntityLinkColumn
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->controllerId) {
            $controller = null;

            if ($this->grid && $this->grid->view) {
                $controller = $this->grid->view->context;
            }

            if (!$controller) {
                $controller = \Yii::$app->controller;
            }

            if ($controller instanceof Controller) {
                $this->controllerId = '/'.ltrim($controller->uniqueId, '/');

                if ($controller->canGetProperty('modelPkAttribute') && $controller->modelPkAttribute) {
                    $this->modelIdAttribute = $controller->modelPkAttribute;
                }
            }
        }

        parent::init();
    }
}
