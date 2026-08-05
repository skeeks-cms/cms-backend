<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendUiAsset;
use skeeks\cms\backend\helpers\BackendIcon;
use skeeks\cms\backend\helpers\BackendUrlHelper;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Canonical header for a backend model page or action drawer.
 *
 * Plain entity names belong in [[title]]. The markup slots [[titleSuffix]],
 * [[media]], [[metaItems]], [[status]], [[toolbar]], [[actions]] and [[side]]
 * are deliberately trusted HTML assembled by the owning package.
 */
class BackendModelHeader extends Widget
{
    /** @var object Model represented by the header. */
    public $model;

    /** @var object|null Owning backend model controller. */
    public $controller;

    /** @var string|null Plain model title. */
    public $title;

    /** @var string Trusted markup rendered after the encoded title. */
    public $titleSuffix = '';

    /** @var string|null Explicit image URL; null enables conventional model image discovery. */
    public $imageSrc;

    /** @var string Trusted media markup. When set, it takes precedence over [[imageSrc]]. */
    public $media = '';

    /** @var string Image alternative text. */
    public $imageAlt = '';

    /** @var array Image element options. */
    public $imageOptions = [];

    /** @var bool Use the round image modifier. */
    public $roundImage = false;

    /** @var bool Add conventional ID/date/author metadata. */
    public $renderDefaultMeta = true;

    /** @var string[] Additional trusted metadata items. */
    public $metaItems = [];

    /** @var string Trusted status markup. */
    public $status = '';

    /** @var string Trusted primary toolbar markup. */
    public $toolbar = '';

    /** @var string|null|false Trusted action markup; null renders the standard delete action. */
    public $actions;

    /** @var string Additional trusted side markup. */
    public $side = '';

    /** @var bool Render the standard delete action when [[actions]] is null. */
    public $renderDeleteAction = true;

    /** @var bool|null Show the back link; null hides it in an empty drawer layout. */
    public $showBackLink;

    /** @var array|string|null Back-link route. */
    public $backUrl;

    /** @var string|null Plain back-link label. */
    public $backLabel;

    /** @var array Root header options. */
    public $options = [];

    public function init()
    {
        parent::init();

        if ($this->controller === null) {
            $this->controller = $this->getView()->context;
        }
        if ($this->model === null && $this->controller && isset($this->controller->model)) {
            $this->model = $this->controller->model;
        }
        if ($this->model === null) {
            throw new InvalidConfigException('BackendModelHeader::model is required.');
        }
        if ($this->title === null) {
            $this->title = $this->controller && isset($this->controller->modelShowName)
                ? $this->controller->modelShowName
                : (isset($this->model->asText) ? $this->model->asText : (string)$this->model);
        }
        if ($this->showBackLink === null) {
            $this->showBackLink = !BackendUrlHelper::createByParams()
                ->setBackendParamsByCurrentRequest()
                ->isEmptyLayout;
        }
        if ($this->backUrl === null && $this->controller && isset($this->controller->defaultAction)) {
            $this->backUrl = [$this->controller->defaultAction];
        }
        if ($this->backLabel === null) {
            $this->backLabel = \Yii::t('skeeks/backend', 'Back');
        }

        Html::addCssClass($this->options, ['sx-model-header', 'sx-model-header--split']);
        BackendUiAsset::register($this->getView());
    }

    public function run()
    {
        $result = '';
        if ($this->showBackLink && $this->backUrl) {
            $result .= Html::tag('div', Html::a(
                BackendIcon::render('arrow-left', ['size' => 16]).' '.Html::encode($this->backLabel),
                Url::to($this->backUrl),
                ['class' => 'sx-model-header__back-link']
            ), ['class' => 'sx-back']);
        }

        $identity = '';
        $media = $this->renderMedia();
        if ($media !== '') {
            $identity .= Html::tag('div', $media, ['class' => 'sx-model-header__media']);
        }

        $content = Html::tag('h1', Html::encode($this->title).$this->titleSuffix, [
            'class' => 'sx-model-header__title',
        ]);
        $meta = $this->renderMeta();
        if ($meta !== '') {
            $content .= Html::tag('div', $meta, ['class' => 'sx-small-info sx-model-header__meta']);
        }
        $identity .= Html::tag('div', $content, ['class' => 'sx-model-header__content']);

        $main = Html::tag('div', Html::tag('div', $identity, [
            'class' => 'sx-model-header__identity',
        ]), ['class' => 'sx-model-header__main']);

        $side = $this->renderSide();
        if ($side !== '') {
            $main .= Html::tag('div', $side, ['class' => 'sx-model-header__side']);
        }

        return $result.Html::tag('div', $main, $this->options);
    }

    protected function renderMedia()
    {
        if ($this->media !== '') {
            return $this->media;
        }

        $imageSrc = $this->imageSrc;
        if ($imageSrc === null) {
            $image = null;
            foreach (['image', 'cmsImage', 'logo'] as $attribute) {
                if (isset($this->model->{$attribute}) && $this->model->{$attribute} && $this->model->{$attribute}->src) {
                    $image = $this->model->{$attribute};
                    break;
                }
            }
            if ($image) {
                $imageSrc = isset($this->model->cms_image_id)
                    ? $image->src
                    : \Yii::$app->imaging->getImagingUrl($image->src,
                        new \skeeks\cms\components\imaging\filters\Thumbnail([
                            'm' => \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND,
                        ]));
            }
        }

        if (!$imageSrc) {
            return '';
        }

        $options = $this->imageOptions;
        Html::addCssClass($options, 'sx-model-header__image');
        if ($this->roundImage) {
            Html::addCssClass($options, 'sx-model-header__image--round');
        }
        $options['alt'] = $this->imageAlt;

        return Html::img($imageSrc, $options);
    }

    protected function renderMeta()
    {
        $items = $this->renderDefaultMeta ? $this->getDefaultMetaItems() : [];
        foreach ($this->metaItems as $item) {
            if ($item !== null && $item !== '') {
                $items[] = $item;
            }
        }

        return implode('', $items);
    }

    protected function getDefaultMetaItems()
    {
        $items = [];
        $modelId = $this->controller && isset($this->controller->modelPkValue)
            ? $this->controller->modelPkValue
            : (isset($this->model->id) ? $this->model->id : null);
        if ($modelId !== null && $modelId !== '') {
            $items[] = Html::tag('span',
                BackendIcon::render('key', ['size' => 13]).' '.Html::encode($modelId),
                [
                    'title'       => \Yii::t('skeeks/backend', 'Record ID'),
                    'data-toggle' => 'tooltip',
                ]
            );
        }
        if (isset($this->model->created_at) && $this->model->created_at) {
            $dateTime = \Yii::$app->formatter->asDatetime($this->model->created_at);
            $items[] = Html::tag('span',
                BackendIcon::render('clock', ['size' => 13]).' '.Html::encode(\Yii::$app->formatter->asDate($this->model->created_at)),
                [
                    'title'       => \Yii::t('skeeks/backend', 'Created at {date}', ['date' => $dateTime]),
                    'data-toggle' => 'tooltip',
                ]
            );
        }
        if (isset($this->model->created_by) && $this->model->created_by && isset($this->model->createdBy) && $this->model->createdBy) {
            $author = $this->model->createdBy;
            $items[] = Html::tag('span',
                BackendIcon::render('user', ['size' => 13]).' '.Html::encode($author->shortDisplayName),
                [
                    'title'       => \Yii::t('skeeks/backend', 'Created by user #{id}', ['id' => $author->id]),
                    'data-toggle' => 'tooltip',
                ]
            );
        }

        return $items;
    }

    protected function renderSide()
    {
        $result = '';
        if ($this->status !== '') {
            $result .= Html::tag('div', $this->status, ['class' => 'sx-model-header__status-stack']);
        }
        if ($this->toolbar !== '') {
            $result .= Html::tag('div', $this->toolbar, ['class' => 'sx-model-header__toolbar']);
        }

        $actions = $this->actions;
        if ($actions === null && $this->renderDeleteAction) {
            $actions = $this->renderDeleteAction();
        }
        if ($actions !== false && $actions !== null && $actions !== '') {
            $result .= Html::tag('div', $actions, ['class' => 'sx-model-header__actions']);
        }
        if ($this->side !== '') {
            $result .= $this->side;
        }

        return $result;
    }

    protected function renderDeleteAction()
    {
        if (!$this->controller || !isset($this->controller->modelActions)) {
            return '';
        }

        $deleteAction = ArrayHelper::getValue($this->controller->modelActions, 'delete');
        if (!$deleteAction || (isset($deleteAction->isVisible) && !$deleteAction->isVisible)) {
            return '';
        }

        $actionData = Json::encode([
            'url'             => $deleteAction->url,
            'isOpenNewWindow' => true,
            'confirm'         => isset($deleteAction->confirm) ? $deleteAction->confirm : '',
            'method'          => isset($deleteAction->method) ? $deleteAction->method : '',
            'request'         => isset($deleteAction->request) ? $deleteAction->request : '',
            'size'            => isset($deleteAction->size) ? $deleteAction->size : '',
        ]);
        $label = \Yii::t('skeeks/backend', 'Delete');

        return Html::a(BackendIcon::render('trash', ['size' => 17]), '#', [
            'onclick'     => "new sx.classes.backend.widgets.Action({$actionData}).go(); return false;",
            'class'       => 'btn btn-default sx-model-header__danger-action',
            'data-toggle' => 'tooltip',
            'title'       => $label,
            'aria-label'  => $label,
        ]);
    }
}
