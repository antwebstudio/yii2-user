<?php

namespace ant\collaborator\models;

use Yii;
use ant\models\ModelClass;
use ant\user\models\User;
use ant\behaviors\TimestampBehavior;
/**
 * This is the model class for table "{{%collaborator_group}}".
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CollaboratorGroupMap[] $collaboratorGroupMaps
 */
class CollaboratorGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return 
        [
            [
                'class' => TimestampBehavior::className(),
            ],
            // [
            //     'class' => \yii\behaviors\BlameableBehavior::className(),
            // ],

        ];
    }

    public static function tableName()
    {
        return '{{%collaborator_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUsers() {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('{{%collaborator_group_map}}', ['collaborator_group_id' => 'id']);
    }

    public function getModel() {
        return $this->hasOne(ModelClass::getClassName($this->model_class_id), ['collaborator_group_id' => 'id']);
    }

}
