<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright (c) 2010 SkeekS
 * @date 13.03.2018
 */

namespace skeeks\cms\backend\models;

use skeeks\cms\models\behaviors\HasJsonFieldsBehavior;
use skeeks\cms\models\CmsUser;
use Yii;

/**
 * This is the model class for table "cms_admin_filter".
 *
 * @property integer      $id
 * @property integer|null $created_by
 * @property integer|null $updated_by
 * @property integer|null $created_at
 * @property integer|null $updated_at
 * @property integer|null $cms_user_id
 * @property integer      $is_default
 * @property string|null  $name
 * @property string       $key
 * @property integer      $priority
 * @property string       $config_jsoned
 *
 * @property integer      $isPublic
 *
 * @property CmsUser      $cmsUser
 * @property CmsUser      $createdBy
 * @property CmsUser      $updatedBy
 *
 * ***
 *
 * @property string       $displayName
 */
class BackendShowing extends \skeeks\cms\models\Core
{
    protected $_isPublic = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backend_showing}}';
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [

            HasJsonFieldsBehavior::className() =>
                [
                    'class'  => HasJsonFieldsBehavior::className(),
                    'fields' => ['config_jsoned'],
                ],
        ]);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['priority', 'created_by', 'updated_by', 'created_at', 'updated_at', 'cms_user_id', 'is_default'], 'integer'],
            [['key'], 'required'],

            [['config_jsoned'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['key'], 'string', 'max' => 255],
            [
                ['cms_user_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => CmsUser::className(),
                'targetAttribute' => ['cms_user_id' => 'id'],
            ],
            [
                ['created_by'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => CmsUser::className(),
                'targetAttribute' => ['created_by' => 'id'],
            ],
            [
                ['updated_by'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => CmsUser::className(),
                'targetAttribute' => ['updated_by' => 'id'],
            ],

            [['cms_user_id'], 'default', 'value' => null],
            [['name'], 'default', 'value' => null],
            [['is_default'], 'default', 'value' => 0],

            [
                ['name'],
                'required',
                'when'     => function($model) {
                    return !$model->is_default;
                },
            ],

        ]);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id'          => Yii::t('skeeks/backend', 'ID'),
            'created_by'  => Yii::t('skeeks/backend', 'Created By'),
            'updated_by'  => Yii::t('skeeks/backend', 'Updated By'),
            'created_at'  => Yii::t('skeeks/backend', 'Created At'),
            'updated_at'  => Yii::t('skeeks/backend', 'Updated At'),
            'cms_user_id' => Yii::t('skeeks/backend', 'Cms User ID'),
            'name'        => Yii::t('skeeks/backend', 'Name'),
            'key'         => Yii::t('skeeks/backend', 'Namespace'),
            'is_default'  => Yii::t('skeeks/backend', 'Is Default'),
            'isPublic'  => Yii::t('skeeks/backend', 'Visible to everyone'),
        ]);
    }
    public function getIsPublic()
    {
        return (int)($this->cms_user_id ? 0 : 1);
    }

    public function setIsPublic($value)
    {
        if ($value) {
            $this->cms_user_id = null;
        } else {
            $this->cms_user_id = \Yii::$app->user->id;
        }
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (!$this->name) {
            return \Yii::t('skeeks/backend', 'Showing');
        }

        return $this->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCmsUser()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'cms_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(CmsUser::className(), ['id' => 'updated_by']);
    }

}