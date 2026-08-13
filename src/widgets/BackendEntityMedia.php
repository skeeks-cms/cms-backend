<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\helpers\BackendIcon;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Canonical circular media for entities in backend collections.
 *
 * A real image keeps its natural colors. Missing media is represented by a
 * semantic inline icon on the backend accent surface.
 */
class BackendEntityMedia extends Widget
{
    /** @var string|null */
    public $imageSrc;

    /** @var object|null storage image model exposing `src` */
    public $image;

    /** @var string */
    public $icon = 'file';

    /** @var string small|default|large */
    public $size = 'large';

    /** @var string */
    public $alt = '';

    /** @var array */
    public $options = [];

    public function run()
    {
        $imageSrc = $this->imageSrc;
        if (!$imageSrc && $this->image && $this->image->src) {
            $imageSrc = \Yii::$app->imaging->thumbnailUrlOnRequest($this->image->src,
                new \skeeks\cms\components\imaging\filters\Thumbnail([
                    'h' => 50,
                    'w' => 50,
                    'm' => \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND,
                ]));
        }

        $options = $this->options;
        Html::addCssClass($options, 'sx-collection-cell__media');

        if ($this->size !== 'default') {
            Html::addCssClass($options, 'sx-collection-cell__media--'.$this->size);
        }

        if ($imageSrc) {
            Html::addCssClass($options, 'sx-collection-cell__media--image');
            $options['alt'] = $this->alt;
            $options['loading'] = $options['loading'] ?? 'lazy';
            $options['decoding'] = $options['decoding'] ?? 'async';

            return Html::img($imageSrc, $options);
        }

        Html::addCssClass($options, 'sx-collection-cell__media--accent');
        $options['aria-hidden'] = 'true';

        return Html::tag('span', BackendIcon::render($this->icon, [
            'size' => $this->size === 'small' ? 14 : 22,
        ]), $options);
    }
}
