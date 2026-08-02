<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\backend\assets\BackendShellMenuAsset;
use skeeks\cms\backend\BackendComponent;
use skeeks\cms\backend\BackendMenuItem;
use yii\base\Widget;

/**
 * Semantic sidebar menu shared by the administration and customer cabinets.
 */
class BackendShellMenuWidget extends Widget
{
    /**
     * Null uses the menu of the current backend component.
     *
     * @var BackendMenuItem[]|null
     */
    public $items;

    /**
     * Render nested branches only for the active path, matching the historical
     * backend menu behavior.
     *
     * @var bool
     */
    public $activeBranchesOnly = true;

    /**
     * @var array
     */
    public $options = [
        'id'    => 'sideNavMenu',
        'class' => 'sx-shell-menu sx-shell-menu--level-1',
    ];

    /**
     * @return string
     */
    public function run()
    {
        BackendShellMenuAsset::register($this->view);

        $items = $this->items;
        if ($items === null) {
            $items = BackendComponent::getCurrent()->menu->items;
        }

        if (!$items) {
            return '';
        }

        return $this->render('backend-shell-menu', [
            'items'              => $items,
            'options'            => $this->options,
            'activeBranchesOnly' => $this->activeBranchesOnly,
        ]);
    }
}
