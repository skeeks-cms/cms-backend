<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use yii\web\JsExpression;

/**
 * Shared infinite-scroll and standard pagination bridge.
 */
class BackendScrollAndSpPager extends \skeeks\yii2\ajaxpager\ScrollAndSpPager
{
    public $triggerTemplate = '<div class="sx-scroll-and-pager ias-trigger col-12" style="margin-bottom: 15px;">
        <button class="btn btn-primary btn-xl btn-block">{text}</button>
    </div>';

    public $triggerText = 'Показать еще';

    public $noneLeftText = '';

    public $spClientOptions = [
        'prevText' => '',
        'nextText' => '',
        'edges'    => '3',
    ];

    public $spClientMobileOptions = [
        'prevText'       => '',
        'nextText'       => '',
        'displayedPages' => '3',
    ];

    public function init()
    {
        if (!$this->eventOnPageChange) {
            $id = $this->id;
            $this->eventOnPageChange = new JsExpression(<<<JS
function(pageNum, scrollOffset, url) {
    if (window.sx && sx.$ && sx.$('#{$id}').data('pagination')) {
        jQuery('#{$id}').data('pagination', sx.$('#{$id}').data('pagination'));
    }

    if (!jQuery('#{$id}').data('pagination')) {
        return;
    }

    var getCurrentPage = jQuery('#{$id}').pagination('getCurrentPage');
    jQuery('#{$id}').pagination('drawPage', getCurrentPage + 1);
}
JS
            );
        }

        if (!$this->eventOnRendered) {
            $this->eventOnRendered = new JsExpression(<<<JS
function() {
    $(document).trigger("scrollAndPagerRendered");
}
JS
            );
        }

        parent::init();
    }
}
