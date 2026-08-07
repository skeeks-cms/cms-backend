<?php

namespace skeeks\cms\backend\grid;

use skeeks\cms\backend\widgets\BackendEntityLink;
use yii\base\InvalidConfigException;
use yii\grid\DataColumn;
use yii\helpers\Html;

/**
 * Renders a primary collection entity as a normal backend link with drawer enhancement.
 */
class BackendEntityLinkColumn extends DataColumn
{
    /** @var bool */
    public $filter = false;

    /** @var string Backend controller route, for example /shop/admin-pay-system. */
    public $controllerId;

    /** @var string|null Explicit action; null opens the first available action by priority. */
    public $action;

    /** @var string Attribute used as the BackendEntityLink model id. */
    public $modelIdAttribute = 'id';

    /** @var string Optional display attribute used instead of the column value. */
    public $viewAttribute = '';

    /** @var string|callable|null Explicit trusted HTML content. */
    public $content;

    /** @var string|array|callable|null Explicit fallback URL. */
    public $url;

    /** @var array|callable Additional controller parameters. */
    public $urlParams = [];

    /** @var array|callable */
    public $linkOptions = [];

    /** @var string */
    public $tag = 'a';

    public function init()
    {
        parent::init();

        if (!$this->controllerId) {
            throw new InvalidConfigException('BackendEntityLinkColumn::controllerId is required.');
        }
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $label = $this->viewAttribute
            ? $model->{$this->viewAttribute}
            : $this->getDataCellValue($model, $key, $index);

        $url = is_callable($this->url)
            ? call_user_func($this->url, $model, $key, $index, $this)
            : $this->url;
        $urlParams = is_callable($this->urlParams)
            ? call_user_func($this->urlParams, $model, $key, $index, $this)
            : $this->urlParams;
        $content = is_callable($this->content)
            ? call_user_func($this->content, $model, $key, $index, $this)
            : $this->content;
        $options = is_callable($this->linkOptions)
            ? call_user_func($this->linkOptions, $model, $key, $index, $this)
            : $this->linkOptions;
        $options = (array)$options;
        Html::addCssClass($options, 'sx-collection-cell__primary');

        return BackendEntityLink::widget([
            'controllerId' => $this->controllerId,
            'action'       => $this->action,
            'modelId'      => $model->{$this->modelIdAttribute},
            'label'        => (string)$label,
            'content'      => $content,
            'url'          => $url,
            'urlParams'    => (array)$urlParams,
            'tag'          => $this->tag,
            'options'      => $options,
        ]);
    }
}
