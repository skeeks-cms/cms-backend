<?php
/**
 * @var yii\web\View $this
 * @var skeeks\cms\backend\BackendMenuItem[] $items
 * @var int $level
 * @var bool $activeBranchesOnly
 */

use skeeks\cms\assets\CmsAsset;
use skeeks\cms\backend\helpers\BackendIcon;
use yii\helpers\Html;

foreach ($items as $item) {
    if (!$item->isVisible) {
        continue;
    }

    $hasChildren = (bool) $item->items;
    $isActive = (bool) $item->isActive;
    $rendersChildren = $hasChildren && (!$activeBranchesOnly || $isActive);
    $submenuId = 'subMenuLevels'.$item->id;
    $itemClasses = [
        'sx-shell-menu__item',
        'sx-shell-menu__item--level-'.$level,
    ];
    $linkClasses = [
        'sx-shell-menu__link',
        'sx-shell-menu__link--level-'.$level,
    ];

    if ($hasChildren) {
        $itemClasses[] = 'sx-shell-menu__item--has-children';
    }
    if ($hasChildren && $isActive) {
        $itemClasses[] = 'sx-shell-menu__item--open';
        $itemClasses[] = 'sx-shell-menu__item--active';
    }
    if ($isActive) {
        $linkClasses[] = 'sx-shell-menu__link--active';
    }

    echo Html::beginTag('li', [
        'class' => implode(' ', $itemClasses),
    ]);

    $linkOptions = [
        'class' => implode(' ', $linkClasses),
    ];
    if ($rendersChildren) {
        $linkOptions['data-sx-shell-menu-target'] = '#'.$submenuId;
        $linkOptions['aria-expanded'] = $isActive ? 'true' : 'false';
        $linkOptions['aria-controls'] = $submenuId;
    }

    echo Html::beginTag('a', array_merge([
        'href' => $item->url,
    ], $linkOptions));

    $iconClasses = ['sx-shell-menu__icon'];
    if ($level > 1) {
        $iconClasses[] = 'sx-shell-menu__icon--nested';
    }

    echo Html::beginTag('span', [
        'class' => implode(' ', $iconClasses),
    ]);
    if ($item->icon) {
        echo Html::tag('i', '', ['class' => $item->icon]);
    } elseif ($item->image) {
        echo Html::img($item->image, ['alt' => '']);
    } else {
        echo Html::img(CmsAsset::getAssetUrl('images/icons/admin-menu/more.svg'), ['alt' => '']);
    }
    echo Html::endTag('span');

    echo Html::tag('span', $item->name, [
        'class' => 'sx-shell-menu__label',
    ]);

    if ($hasChildren) {
        echo Html::tag(
            'span',
            BackendIcon::render('chevron-right', ['size' => 14]),
            ['class' => 'sx-shell-menu__control']
        );
        if ($level === 1) {
            echo Html::tag('span', '', ['class' => 'sx-shell-menu__indicator']);
        }
    }

    echo Html::endTag('a');

    if ($rendersChildren) {
        echo Html::beginTag('ul', [
            'id'    => $submenuId,
            'class' => 'sx-shell-menu sx-shell-menu--level-'.($level + 1),
        ]);
        echo $this->render('backend-shell-menu-items', [
            'items'              => $item->items,
            'level'              => $level + 1,
            'activeBranchesOnly' => $activeBranchesOnly,
        ]);
        echo Html::endTag('ul');
    }

    echo Html::endTag('li');
}
