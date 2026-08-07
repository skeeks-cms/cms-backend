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
}
