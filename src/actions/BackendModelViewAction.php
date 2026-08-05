<?php

namespace skeeks\cms\backend\actions;

/**
 * Safe read-only model card used as the default entity-link action.
 */
class BackendModelViewAction extends BackendModelAction
{
    /**
     * DetailView attributes. A callable receives this action.
     *
     * By default only the controller's display attribute and primary key are
     * shown. Packages can opt in to additional, non-sensitive attributes.
     *
     * @var array|callable|null
     */
    public $attributes;

    public $defaultView = '@skeeks/cms/backend/actions/views/model-view';

    public function init()
    {
        // Reading the same entity that is already visible in the grid must not
        // introduce a separate generated permission. Controller-level access
        // remains in force.
        $this->generateAccess = false;

        if (!$this->icon) {
            $this->icon = 'fa fa-eye';
        }

        if (!$this->name) {
            $this->name = \Yii::t('skeeks/backend', 'View');
        }

        parent::init();
    }

    public function getResolvedAttributes()
    {
        if ($this->attributes !== null) {
            return is_callable($this->attributes)
                ? (array) call_user_func($this->attributes, $this)
                : (array) $this->attributes;
        }

        $attributes = [];
        $showAttribute = $this->controller->modelShowAttribute;
        $pkAttribute = $this->controller->modelPkAttribute;

        if ($showAttribute) {
            $attributes[] = $showAttribute;
        }

        if ($pkAttribute && $pkAttribute !== $showAttribute) {
            $attributes[] = $pkAttribute;
        }

        return $attributes;
    }

    public function run()
    {
        if ($this->callback && is_callable($this->callback)) {
            return call_user_func($this->callback, $this);
        }

        return $this->render($this->defaultView, [
            'model'      => $this->model,
            'attributes' => $this->resolvedAttributes,
        ]);
    }
}
