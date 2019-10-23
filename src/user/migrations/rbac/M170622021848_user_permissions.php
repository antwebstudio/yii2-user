<?php

namespace ant\user\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;
use ant\rbac\rules\IsOwnModelRule;
use ant\user\models\UserProfile;
use frontend\modules\user\controllers\SettingController;
use frontend\modules\user\controllers\ProfilesController;
use backend\modules\user\controllers\UserController;
use backend\modules\user\controllers\InviteController;
use backend\modules\user\controllers\ConfigController;

class M170622021848_user_permissions extends Migration
{
	protected $permissions;

	public function init() {
		$this->permissions = [
			\frontend\modules\user\controllers\DashboardController::className() => [
				'index' => ['User Dashboard', [Role::ROLE_USER]],
			],
			SettingController::className() => [
				'index' => ['Update user account setting', [Role::ROLE_USER]],
				'email' => ['Change user account email setting', [Role::ROLE_USER]],
				'password' => ['Change user account password', [Role::ROLE_USER]],
				'zone-list' => ['Retrieve state list', [Role::ROLE_USER]],
				'avatar-upload' => ['Upload picture', [Role::ROLE_USER]],
				'avatar-delete' => ['Delete picture', [Role::ROLE_USER]],
				'token-change-email' => ['Change user account email using email link', [Role::ROLE_GUEST]],
			],
			ProfilesController::className() => [
				'index' => ['Update user profiles', [Role::ROLE_USER]],
				//'view' => ['View user profiles', [Role::ROLE_USER]],
				'create' => ['Create user profile', [Role::ROLE_USER]],
				'update' => ['Update user profile', [Role::ROLE_USER]],
				'delete' => ['Delete user profile', [Role::ROLE_USER]],
			],
			UserProfile::className() => [
				'view' => ['View user profile', [Role::ROLE_USER], 'ruleName' => IsOwnModelRule::className()],
				'delete' => ['Delete user profile', [Role::ROLE_USER], 'ruleName' => IsOwnModelRule::className()],
			],
			UserController::className() => [
				'index' => ['Show user', [Role::ROLE_ADMIN]],
				'update' => ['Update user', [Role::ROLE_ADMIN]],
				'delete' => ['Delete user', [Role::ROLE_ADMIN]],
			],
			InviteController::className() => [
				'index' => ['Show user profiles', [Role::ROLE_ADMIN]],
				'resend' => ['Resend invite', [Role::ROLE_ADMIN]],
				'update' => ['Update user profile', [Role::ROLE_ADMIN]],
				'create' => ['Create invite', [Role::ROLE_ADMIN]],
				'send'   => ['Send invite request', [Role::ROLE_ADMIN]],
				'delete' => ['Delete invite profile', [Role::ROLE_ADMIN]]
			],
			ConfigController::className() => [
				'index' => ['Show registered users', [Role::ROLE_ADMIN]],
				'update' => ['Update registered users', [Role::ROLE_ADMIN]],
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
