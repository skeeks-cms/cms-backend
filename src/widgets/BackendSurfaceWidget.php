<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use Closure;
use skeeks\cms\backend\assets\BackendUiAsset;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Canonical content surface for Admin, UPA and customer cabinets.
 *
 * Title and hint are plain text. Actions, content and footer are trusted HTML,
 * arrays of trusted HTML fragments, or closures receiving this widget.
 * Supports both BackendSurfaceWidget::widget() and begin()/end() usage.
 */
class BackendSurfaceWidget extends Widget
{
    /** @var string Plain heading text. */
    public $title = '';

    /** @var string Plain supporting copy. */
    public $hint = '';

    /** @var string|array|Closure Trusted actions HTML. */
    public $actions = '';

    /** @var string|array|Closure|null Trusted body HTML; null captures begin/end output. */
    public $content;

    /** @var string|array|Closure Trusted footer HTML. */
    public $footer = '';

    /** @var bool Use the canonical raised shadow. */
    public $raised = false;

    /** @var bool Clip children to the surface radius. */
    public $clip = false;

    /** @var bool Stack header/actions/footer on narrow screens. */
    public $responsive = false;

    /** @var bool Separate the header from the body. */
    public $headerBordered = false;

    /** @var bool Remove canonical body padding for edge-to-edge content. */
    public $bodyFlush = false;

    /** @var bool Stretch direct footer children to available width. */
    public $footerStretch = false;

    /** @var string Root HTML tag. */
    public $tag = 'section';

    /** @var string Heading HTML tag. */
    public $titleTag = 'h2';

    /** @var array Root HTML options. */
    public $options = [];

    /** @var array Header HTML options. */
    public $headerOptions = [];

    /** @var array Title HTML options. */
    public $titleOptions = [];

    /** @var array Hint HTML options. */
    public $hintOptions = [];

    /** @var array Actions HTML options. */
    public $actionsOptions = [];

    /** @var array Body HTML options. */
    public $bodyOptions = [];

    /** @var array Footer HTML options. */
    public $footerOptions = [];

    /** @var bool */
    private $_captureContent = false;

    public function init()
    {
        parent::init();

        BackendUiAsset::register($this->getView());

        Html::addCssClass($this->options, 'sx-surface');
        if ($this->raised) {
            Html::addCssClass($this->options, 'sx-surface--raised');
        }
        if ($this->clip) {
            Html::addCssClass($this->options, 'sx-surface--clip');
        }
        if ($this->responsive) {
            Html::addCssClass($this->options, 'sx-surface--responsive');
        }

        Html::addCssClass($this->headerOptions, 'sx-surface__header');
        if ($this->headerBordered) {
            Html::addCssClass($this->headerOptions, 'sx-surface__header--bordered');
        }
        Html::addCssClass($this->titleOptions, 'sx-surface__title');
        Html::addCssClass($this->hintOptions, 'sx-surface__hint');
        Html::addCssClass($this->actionsOptions, 'sx-surface__actions');
        Html::addCssClass($this->bodyOptions, 'sx-surface__body');
        if ($this->bodyFlush) {
            Html::addCssClass($this->bodyOptions, 'sx-surface__body--flush');
        }
        Html::addCssClass($this->footerOptions, 'sx-surface__footer');
        if ($this->footerStretch) {
            Html::addCssClass($this->footerOptions, 'sx-surface__footer--stretch');
        }

        if ($this->title !== '') {
            if (!isset($this->titleOptions['id'])) {
                $this->titleOptions['id'] = $this->getId().'-title';
            }
            if (!isset($this->options['aria-label']) && !isset($this->options['aria-labelledby'])) {
                $this->options['aria-labelledby'] = $this->titleOptions['id'];
            }
        }

        if ($this->content === null) {
            $this->_captureContent = true;
            ob_start();
            ob_implicit_flush(false);
        }
    }

    public function run()
    {
        if ($this->_captureContent) {
            $this->content = ob_get_clean();
            $this->_captureContent = false;
        }

        $content = $this->renderHeader();
        $body = $this->renderHtml($this->content);
        if ($body !== '') {
            $content .= Html::tag('div', $body, $this->bodyOptions);
        }

        $footer = $this->renderHtml($this->footer);
        if ($footer !== '') {
            $content .= Html::tag('footer', $footer, $this->footerOptions);
        }

        return Html::tag($this->tag, $content, $this->options);
    }

    /**
     * @return string
     */
    protected function renderHeader()
    {
        $actions = $this->renderHtml($this->actions);
        if ($this->title === '' && $this->hint === '' && $actions === '') {
            return '';
        }

        $copy = '';
        if ($this->title !== '') {
            $copy .= Html::tag($this->titleTag, Html::encode($this->title), $this->titleOptions);
        }
        if ($this->hint !== '') {
            $copy .= Html::tag('p', Html::encode($this->hint), $this->hintOptions);
        }

        $content = Html::tag('div', $copy, ['class' => 'sx-surface__heading']);
        if ($actions !== '') {
            $content .= Html::tag('div', $actions, $this->actionsOptions);
        }

        return Html::tag('header', $content, $this->headerOptions);
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function renderHtml($value)
    {
        if ($value instanceof Closure) {
            $value = call_user_func($value, $this);
        }

        if (is_array($value)) {
            $result = '';
            foreach ($value as $fragment) {
                $result .= $this->renderHtml($fragment);
            }

            return $result;
        }

        return $value === null ? '' : (string)$value;
    }
}
