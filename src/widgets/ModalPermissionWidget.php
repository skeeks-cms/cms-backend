<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 16.03.2017
 */

namespace skeeks\cms\backend\widgets;

use skeeks\cms\IHasPermissions;
use yii\base\InvalidConfigException;
use yii\bootstrap\Modal;

/**
 * Class ModalPermissionWidget
 *
 * @package skeeks\cms\backend\widgets
 */
class ModalPermissionWidget extends Modal
{

    public $size = self::SIZE_LARGE;

    /**
     * @var null
     */
    public $viewFile = 'modal-permissions-widget';

    /**
     * @var IHasPermissions
     */
    public $controller = null;

    public $standartToggleButton = true;

    public function init()
    {
        if (!$this->controller)
        {
            throw new InvalidConfigException('Property controller not be null: ' . static::class);
        }

        if (isset($this->header) && $this->header !== false)
        {
            $this->header = \Yii::t('skeeks/backend', 'Access settings');
        }

        if ($this->toggleButton === false && $this->standartToggleButton)
        {
            $this->toggleButton = [
                'tag' => 'a',
                'class' => "btn btn-default btn-primary",
                'label' => '<i class="glyphicon glyphicon-exclamation-sign" data-sx-widget="tooltip-b" data-original-title="' . \Yii::t('skeeks/backend','Setting up access to this section') . '"></i>'
            ];
        }

        parent::init();
    }

    public function run()
    {
        if ($this->viewFile)
        {
            echo $this->render($this->viewFile);
        }

        parent::run();
    }
}