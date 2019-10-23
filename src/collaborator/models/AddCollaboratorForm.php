<?php

namespace ant\collaborator\models;

use ant\user\models\User;
use ant\collaborator\models\CollaboratorGroup;

class AddCollaboratorForm extends \yii\base\Model {
    public $user;
    public $collaboratorGroup;

    public function rules() {
        return [
            [['user', 'collaboratorGroup'], 'required'],
            [['user', 'collaboratorGroup'], 'unique', 'targetClass' => 'ant\collaborator\models\CollaboratorGroupMap', 'targetAttribute' => ['user' => 'user_id', 'collaboratorGroup' => 'collaborator_group_id']],
        ];
    }

    public function getCollaboratorGroup() {
        return CollaboratorGroup::findOne($this->collaboratorGroup);
    }

    public function getUser() {
        return User::findOne($this->user);
    }

    public function canAccessBy($userId) {
        return $this->getCollaboratorGroup()->model->haveCollaborator($userId);
    }

    public function save() {
        if ($this->validate()) {
            $group = $this->getCollaboratorGroup();
            $group->link('users', $this->getUser());

            if (!$group->save()) throw new \Exception(print_r($group->errors, 1));

            return true;
        }
    }
}