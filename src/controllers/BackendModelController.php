<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 * @date 06.03.2017
 */
namespace skeeks\cms\backend\controllers;

use skeeks\cms\backend\BackendInfoInterface;
use skeeks\cms\backend\BackendModelControllerInterface;
use skeeks\cms\backend\BackendPermissionsInterface;
use skeeks\cms\backend\BackendUrlInterface;
use skeeks\cms\backend\BackendUrlRule;
use skeeks\cms\backend\traits\BackendInfoTrait;
use skeeks\cms\backend\traits\BackendModelControllerTrait;
use skeeks\cms\helpers\StringHelper;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class BackendModelController
 * @package skeeks\cms\backend\controllers
 */
class BackendModelController extends BackendController
    implements BackendModelControllerInterface
{
    use BackendModelControllerTrait;
}