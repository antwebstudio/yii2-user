<?php

namespace ant\user\models;

use Yii;

/**
 * This is the model class for table "ks_user_identity".
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $value
 *
 * @property User $user
 */
class UserIdentity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_identity}}';
    }

    public function behaviors() {
        return [
            'configurable' => [
                'class' => 'ant\behaviors\ConfigurableModelBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->getCombinedRules([
            [['user_id'], 'integer'],
            [['value'], 'required'],
            [['type'], 'string', 'max' => 10],
            [['value'], 'string', 'max' => 20],
            [['value'], 'unique', 'message' => '{attribute} "{value}" is already exist. ', 'targetAttribute' => ['type', 'value']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->getCombinedAttributeLabels([
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'value' => 'Value',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
