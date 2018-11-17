<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\backend\actions;

/**
 * Trait TBackendModelUpdateAction
 * @package skeeks\cms\backend\actions
 */
trait TBackendModelUpdateAction
{
    /**
     * @var bool
     */
    public $modelValidate = true;

    /**
     * @var string
     */
    public $afterContent = '';

    /**
     * @var string
     */
    public $beforeContent = '';

    /**
     * @var string
     */
    public $afterSaveUrl = '';

    /**
     * @var string
     */
    public $successMessage = '';

    /**
     * @var bool
     */
    public $isSaveFormModels = true;


    /**
     * @var array|callable
     */
    public $fields = [];

    /**
     * @var array
     */
    public $formModels = [];
}