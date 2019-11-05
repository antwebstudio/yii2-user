<?php

namespace ant\user\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M191004071246Permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\user\controllers\ActivationController::className() => [
				'activation' => ['User Activation', [Role::ROLE_USER]],
				'resend-code' => ['Resend user activation code', [Role::ROLE_USER]],
				'token-activation' => ['Token Activation', [Role::ROLE_GUEST]],
				'new-password-activate' => ['Activate new password', [Role::ROLE_GUEST]],
				'request-password-reset' => ['Request password reset', [Role::ROLE_GUEST]],
				'reset-password' => ['Reset Password', [Role::ROLE_GUEST]],
			],
			\ant\user\backend\controllers\UserController::className() => [
				'index' => ['Show user', [Role::ROLE_ADMIN]],
				'update' => ['Update user', [Role::ROLE_ADMIN]],
				'delete' => ['Delete user', [Role::ROLE_ADMIN]],
				'update-password' => ['Update user password', [Role::ROLE_DEVELOPER]],
			],
		];
		
		parent::init();
	}
	
	public function up()
    {
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
