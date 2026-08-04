<?php

use skeeks\cms\backend\helpers\BackendIcon;
use yii\helpers\Html;
use yii\helpers\Json;

/* @var $customizer array */

$scope = isset($customizer['scope']) && $customizer['scope'] === 'upa' ? 'upa' : 'admin';
$scopeLabel = $scope === 'upa'
    ? \Yii::t('skeeks/backend', 'Client cabinet')
    : \Yii::t('skeeks/backend', 'Administration');
$titleId = 'sx-theme-customizer-title-'.$scope;
$fields = [
    \Yii::t('skeeks/backend', 'Brand and accent') => [
        'accent' => \Yii::t('skeeks/backend', 'Accent'),
    ],
    \Yii::t('skeeks/backend', 'Surfaces') => [
        'canvas'       => \Yii::t('skeeks/backend', 'Page background'),
        'surface'      => \Yii::t('skeeks/backend', 'Main surface'),
        'surfaceMuted' => \Yii::t('skeeks/backend', 'Muted surface'),
    ],
    \Yii::t('skeeks/backend', 'Text and borders') => [
        'text'      => \Yii::t('skeeks/backend', 'Main text'),
        'textMuted' => \Yii::t('skeeks/backend', 'Muted text'),
        'border'    => \Yii::t('skeeks/backend', 'Borders'),
    ],
    \Yii::t('skeeks/backend', 'Statuses') => [
        'success' => \Yii::t('skeeks/backend', 'Success'),
        'warning' => \Yii::t('skeeks/backend', 'Warning'),
        'danger'  => \Yii::t('skeeks/backend', 'Danger'),
    ],
];
?>
<div
    class="sx-theme-customizer"
    data-sx-theme-customizer
    data-sx-theme-customizer-scope="<?= Html::encode($scope); ?>"
    data-sx-theme-customizer-config="<?= Html::encode(Json::htmlEncode($customizer)); ?>"
    hidden
>
    <button
        class="sx-theme-customizer__backdrop"
        type="button"
        data-sx-theme-customizer-close
        aria-label="<?= Html::encode(\Yii::t('skeeks/backend', 'Close theme settings')); ?>"
    ></button>
    <section
        class="sx-theme-customizer__panel"
        role="dialog"
        tabindex="-1"
        aria-modal="true"
        aria-labelledby="<?= Html::encode($titleId); ?>"
    >
        <header class="sx-theme-customizer__header">
            <div>
                <h2 id="<?= Html::encode($titleId); ?>" class="sx-theme-customizer__title">
                    <?= Html::encode(\Yii::t('skeeks/backend', 'Theme settings')); ?>
                </h2>
                <p class="sx-theme-customizer__context">
                    <?= Html::encode($scopeLabel); ?> ·
                    <span data-sx-theme-customizer-mode-label></span>
                </p>
            </div>
            <button
                class="sx-theme-customizer__close"
                type="button"
                data-sx-theme-customizer-close
                aria-label="<?= Html::encode(\Yii::t('skeeks/backend', 'Close')); ?>"
            >
                <?= BackendIcon::render('close', ['size' => 20]); ?>
            </button>
        </header>

        <div class="sx-theme-customizer__body">
            <div class="sx-theme-customizer__modes" aria-label="<?= Html::encode(\Yii::t('skeeks/backend', 'Color scheme')); ?>">
                <button type="button" data-sx-theme-customizer-mode="light">
                    <?= BackendIcon::render('sun', ['size' => 15]); ?>
                    <?= Html::encode(\Yii::t('skeeks/backend', 'Light')); ?>
                </button>
                <button type="button" data-sx-theme-customizer-mode="dark">
                    <?= BackendIcon::render('moon', ['size' => 15]); ?>
                    <?= Html::encode(\Yii::t('skeeks/backend', 'Dark')); ?>
                </button>
            </div>

            <p class="sx-theme-customizer__hint">
                <?= Html::encode(\Yii::t('skeeks/backend', 'Changes are previewed immediately and are not saved until you choose an action below.')); ?>
            </p>

            <fieldset class="sx-theme-customizer__group sx-theme-customizer__group--header">
                <legend><?= Html::encode(\Yii::t('skeeks/backend', 'Header')); ?></legend>
                <div class="sx-theme-customizer__modes sx-theme-customizer__header-modes" aria-label="<?= Html::encode(\Yii::t('skeeks/backend', 'Header appearance')); ?>">
                    <button type="button" data-sx-theme-header="dark">
                        <?= Html::encode(\Yii::t('skeeks/backend', 'Dark')); ?>
                    </button>
                    <button type="button" data-sx-theme-header="light">
                        <?= Html::encode(\Yii::t('skeeks/backend', 'Light')); ?>
                    </button>
                    <button type="button" data-sx-theme-header="theme">
                        <?= Html::encode(\Yii::t('skeeks/backend', 'As theme')); ?>
                    </button>
                </div>
                <p class="sx-theme-customizer__field-hint">
                    <?= Html::encode(\Yii::t('skeeks/backend', 'Configured separately for the selected color scheme.')); ?>
                </p>
            </fieldset>

            <?php foreach ($fields as $groupLabel => $groupFields) : ?>
                <fieldset class="sx-theme-customizer__group">
                    <legend><?= Html::encode($groupLabel); ?></legend>
                    <?php foreach ($groupFields as $name => $label) : ?>
                        <label class="sx-theme-customizer__field">
                            <span><?= Html::encode($label); ?></span>
                            <span class="sx-theme-customizer__control">
                                <input
                                    type="color"
                                    data-sx-theme-color="<?= Html::encode($name); ?>"
                                    aria-label="<?= Html::encode($label); ?>"
                                >
                                <input
                                    type="text"
                                    data-sx-theme-hex="<?= Html::encode($name); ?>"
                                    aria-label="<?= Html::encode($label.' HEX'); ?>"
                                    inputmode="text"
                                    maxlength="7"
                                    spellcheck="false"
                                >
                            </span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        </div>

        <footer class="sx-theme-customizer__footer">
            <button class="sx-button sx-button--secondary sx-button--sm sx-theme-customizer__reset" type="button" data-sx-theme-customizer-reset>
                <?= Html::encode(\Yii::t('skeeks/backend', 'Reset my settings')); ?>
            </button>
            <div class="sx-theme-customizer__footer-actions">
                <?php if (!empty($customizer['canApplyDefault']) && !empty($customizer['saveDefaultUrl'])) : ?>
                    <button class="sx-button sx-button--secondary sx-button--sm" type="button" data-sx-theme-customizer-save-default>
                        <?= Html::encode(\Yii::t('skeeks/backend', 'Apply for everyone')); ?>
                    </button>
                <?php endif; ?>
                <button class="sx-button sx-button--primary sx-button--sm" type="button" data-sx-theme-customizer-save>
                    <?= Html::encode(\Yii::t('skeeks/backend', 'Save for me')); ?>
                </button>
            </div>
        </footer>
    </section>
</div>
