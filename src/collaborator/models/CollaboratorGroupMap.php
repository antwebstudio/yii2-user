<?php

namespace ant\collaborator\models;

use Yii;

/**
 * This is the model class for table "em_collaborator_group_map".
 *
 * @property integer $id
 * @property integer $collaborator_group_id
 * @property integer $user_id
 *
 * @property CollaboratorGroup $collaboratorGroup
 * @property User $user
 */
class CollaboratorGroupMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collaborator_group_map}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collaborator_group_id', 'user_id'], 'integer'],
            [['collaborator_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => CollaboratorGroup::className(), 'targetAttribute' => ['collaborator_group_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collaborator_group_id' => 'Collaborator Group ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCollaboratorGroup()
    {
        return $this->hasOne(CollaboratorGroup::className(), ['id' => 'collaborator_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
