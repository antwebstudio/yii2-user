<?php

namespace ant\user\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M180226041700_user_backend_permissions extends Migration
{
	protected $permissions;

	public function init() {
		$this->permissions = [
			\backend\modules\user\controllers\UserController::className() => [
				'index' => ['Manage user profiles', [Role::ROLE_ADMIN]],
				'view' => ['View user profile detail', [Role::ROLE_USER]],
				'create' => ['Create user profile', [Role::ROLE_ADMIN]],
				'update' => ['Update user profile', [Role::ROLE_ADMIN]],
				'update-config' => ['Update user config', [Role::ROLE_ADMIN]],
				'update-contact' => ['Update user contact', [Role::ROLE_ADMIN]],
				'update-identity' => ['Update user identity info', [Role::ROLE_ADMIN]],
				'update-profile-data' => ['Update user profile details', [Role::ROLE_ADMIN]],
				'delete' => ['Delete user profile', [Role::ROLE_ADMIN]],
				'avatar-upload' => ['Upload user avatar', [Role::ROLE_ADMIN]],
				'avatar-delete' => ['Delete user avatar', [Role::ROLE_ADMIN]],
				'approve' => ['Approve registered user', [Role::ROLE_ADMIN]],
				'unapprove' => ['Unapprove registered user', [Role::ROLE_ADMIN]],
				'activate' => ['Activate registered user', [Role::ROLE_ADMIN]],
				'unactivate' => ['Unactivate registered user', [Role::ROLE_ADMIN]],
				'email-activation-code' => ['Email activation code to registered user', [Role::ROLE_ADMIN]],
				'role' => ['Setting user role', [Role::ROLE_ADMIN]],
			],
			\backend\modules\user\controllers\ProfilesController::className() => [
				'index' => ['Manage user profiles', [Role::ROLE_ADMIN]],
				//'view' => ['View user profiles', [Role::ROLE_USER]],
				'create' => ['Create user profile', [Role::ROLE_ADMIN]],
				'update' => ['Update user profile', [Role::ROLE_ADMIN]],
				'delete' => ['Delete user profile', [Role::ROLE_ADMIN]],
			],
			\backend\modules\user\controllers\SettingController::className() => [
				'index' => ['Update user profiles', [Role::ROLE_ADMIN]],
				'password' => ['Update own account password', [Role::ROLE_ADMIN]],
			],
			\backend\modules\user\controllers\ConfigController::className() => [
				'main' => ['Manage user config', [Role::ROLE_ADMIN]],
				'index' => ['Manage user config', [Role::ROLE_ADMIN]],
				'view' => ['View user config', [Role::ROLE_ADMIN]],
				'create' => ['Create user config', [Role::ROLE_ADMIN]],
				'update' => ['Update user config', [Role::ROLE_ADMIN]],
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
