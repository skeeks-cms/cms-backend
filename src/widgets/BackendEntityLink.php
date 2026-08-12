<?php

namespace skeeks\cms\backend\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Renders a semantic link that opens a configured backend entity action.
 *
 * The href remains a normal action URL as a keyboard/no-JavaScript fallback;
 * AjaxControllerActionsWidget owns the standard backend drawer behavior.
 */
class BackendEntityLink extends Widget
{
    /** @var string Backend controller route, for example /cms/admin-user. */
    public $controllerId;

    /** @var int|string */
    public $modelId;

    /** @var string|null Explicit backend action; null opens the first available action. */
    public $action;

    /** @var array Additional parameters required by the backend controller action. */
    public $urlParams = [];

    /** @var string|null Plain-text label used when content is not provided. */
    public $label;

    /** @var string|null Trusted HTML content. */
    public $content;

    /** @var string|array|null Explicit fallback URL. */
    public $url;

    /** @var array */
    public $options = [];

    /** @var string */
    public $tag = 'a';

    /** @var bool Automatically suppress the link when the current user cannot access its controller. */
    public $checkAccess = true;

    /** @var string|null Explicit permission name; defaults to the normalized controller route. */
    public $permissionName;

    public function init()
    {
        parent::init();

        if (!$this->controllerId) {
            throw new InvalidConfigException('BackendEntityLink::controllerId is required.');
        }

        if ($this->modelId === null || $this->modelId === '') {
            throw new InvalidConfigException('BackendEntityLink::modelId is required.');
        }
    }

    public function run()
    {
        $content = $this->content;
        if ($content === null) {
            $content = Html::encode($this->label);
        }

        if (!$this->isLinkAllowed()) {
            $options = $this->options;
            unset($options['href'], $options['onclick'], $options['data']);
            Html::removeCssClass($options, ['sx-entity-link', 'sx-btn-ajax-actions']);
            Html::addCssClass($options, 'sx-entity-label');

            return Html::tag('span', $content, $options);
        }

        $fallbackAction = $this->action ?: 'update';
        $route = '/'.ltrim($this->controllerId, '/').'/'.$fallbackAction;
        $fallbackUrl = $this->url ?: array_merge([$route, 'pk' => $this->modelId], $this->urlParams);
        $options = array_merge([
            'class'     => 'sx-entity-link',
            'href'      => Url::to($fallbackUrl),
            'data-pjax' => '0',
        ], $this->options);
        Html::addCssClass($options, 'sx-entity-link');

        return AjaxControllerActionsWidget::widget([
            'controllerId'            => $this->controllerId,
            'modelId'                 => $this->modelId,
            'urlParams'               => $this->urlParams,
            'actionOnClick'            => $this->action,
            'isRunFirstActionOnClick' => !$this->action,
            'tag'                     => $this->tag,
            'content'                 => $content,
            'options'                 => $options,
        ]);
    }

    protected function isLinkAllowed()
    {
        if (!$this->checkAccess || !\Yii::$app->has('authManager') || !\Yii::$app->has('user')) {
            return true;
        }

        $permissionName = $this->permissionName ?: ltrim($this->controllerId, '/');
        if (!\Yii::$app->authManager->getPermission($permissionName)) {
            return true;
        }

        return !\Yii::$app->user->isGuest && \Yii::$app->user->can($permissionName);
    }
}
