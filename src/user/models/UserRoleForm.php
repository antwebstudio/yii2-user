<?php
namespace ant\user\models;

use Yii;

use yii\base\Model;
use ant\user\models\User;
use ant\rbac\Role;

class UserRoleForm extends Model
{
    public $userId;
    public $roles;
    public $disabled = ['guest'];

    protected $_oldRoles;

    public function rules() {
        return [
            [['roles'], 'safe'],
        ];
    }

    public function init() {
        $roles = $this->auth->getRolesByUser($this->userId);
        $roles = array_keys($roles);
        $this->_oldRoles = $roles;
        $this->roles = $roles;
    }

    public function getAvailableRoles() {
        $roles = $this->auth->getRoles();
        $roles = array_keys($roles);
        return array_combine($roles, $roles);
    }

    public function isOptionDisabled($value) {
        if (in_array($value, $this->disabled)) return true;
        return !Yii::$app->user->can($value);
    }

    public function save() {
        foreach ($this->getAvailableRoles() as $role) {
            if (!$this->isOptionDisabled($role)) {
                if (in_array($role, $this->roles)) {
                    $this->addRole($role);
                } else {
                    $this->removeRole($role);
                }
            }
        }
        return true;
    }

    protected function getAuth() {
        return Yii::$app->authManager;
    }

    protected function addRole($role) {
        if (!in_array($role, $this->_oldRoles)) {
            $role = $this->auth->getRole($role);
            $this->auth->assign($role, $this->userId);
        }
    }

    protected function removeRole($role) {
        if (in_array($role, $this->_oldRoles)) {
            $role = $this->auth->getRole($role);
            $this->auth->revoke($role, $this->userId);
        }
    }
}