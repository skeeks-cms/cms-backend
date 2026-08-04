<?php

use skeeks\cms\backend\helpers\BackendIcon;
use yii\helpers\Html;
use yii\helpers\Json;

/* @var $containerClass string */
/* @var $toggleClass string */
/* @var $showLabel bool */
/* @var $customizer array */

$lightLabel = \Yii::t('skeeks/cms', 'Light theme');
$darkLabel = \Yii::t('skeeks/cms', 'Dark theme');
?>
<div class="<?= Html::encode(trim('sx-theme-switcher '.$containerClass)); ?>">
    <button
        class="<?= Html::encode(trim('sx-theme-switcher__toggle '.$toggleClass)); ?>"
        type="button"
        data-sx-theme-mode-toggle
        data-sx-theme-light-label="<?= Html::encode($lightLabel); ?>"
        data-sx-theme-dark-label="<?= Html::encode($darkLabel); ?>"
        title="<?= Html::encode($darkLabel); ?>"
        aria-label="<?= Html::encode($darkLabel); ?>"
        aria-pressed="false"
    >
        <span class="sx-theme-switcher__track" aria-hidden="true">
            <?= BackendIcon::render('sun', [
                'size'  => 14,
                'class' => 'sx-theme-switcher__sun',
            ]); ?>
            <?= BackendIcon::render('moon', [
                'size'  => 14,
                'class' => 'sx-theme-switcher__moon',
            ]); ?>
            <span class="sx-theme-switcher__thumb"></span>
        </span>
        <?php if ($showLabel) : ?>
            <span class="sx-theme-switcher__current-label"></span>
        <?php endif; ?>
    </button>

    <?php if ($customizer) : ?>
        <button
            class="sx-theme-switcher__customize"
            type="button"
            data-sx-theme-customizer-lazy
            data-sx-theme-customizer-config="<?= Html::encode(Json::htmlEncode($customizer)); ?>"
            title="<?= Html::encode(\Yii::t('skeeks/backend', 'Customize theme')); ?>"
            aria-label="<?= Html::encode(\Yii::t('skeeks/backend', 'Customize theme')); ?>"
            aria-haspopup="dialog"
            aria-expanded="false"
        >
            <?= BackendIcon::render('palette', ['size' => 17]); ?>
        </button>
    <?php endif; ?>
</div>
