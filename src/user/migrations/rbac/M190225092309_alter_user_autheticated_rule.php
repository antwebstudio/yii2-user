<?php

namespace ant\user\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;
use ant\rbac\rules\AuthenticatedUserRule;

class M190225092309_alter_user_autheticated_rule extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
            /*
			FolderController::className() => [
				'my-file' => ['View own file', [Role::ROLE_USER]],
			],
            */
		];
		
		parent::init();
	}
	
	public function up()
    {
		/* 
		 * Get all data of "user" so that can be added back after "user" is removed
		 */
		
		// Get all parents 
		$parents = (new \yii\db\Query)->select('parent')
			->from(\Yii::$app->authManager->itemChildTable)
			->where(['child' => Role::ROLE_USER])->all();
			
		// Get all users
		$users = $this->auth->getUserIdsByRole(Role::ROLE_USER);
		
		// Get all children
		$children = (new \yii\db\Query)->select('child')
			->from(\Yii::$app->authManager->itemChildTable)
			->where(['parent' => Role::ROLE_USER])->all();
		
		// Remove "user" role
		$userRole = $this->auth->getRole(Role::ROLE_USER);
		$this->auth->remove($userRole);
		
		// Add back "user" role
		$userRole = $this->auth->createRole(Role::ROLE_USER);
		$userRole->ruleName = AuthenticatedUserRule::className();
		$this->auth->add($userRole);
		
		/*
		 * Add back all data for "user"
		 */
		 
		// Add back all parents
		foreach ($parents as $parent) {
			$parent = $this->auth->getRole($parent['parent']);
			$this->auth->addChild($parent, $userRole);
		}
		
		//throw new \Exception(print_r($children, 1));
		
		// Add back all children
		foreach ($children as $child) {
			$child = $this->auth->getPermission($child['child']);
			$this->auth->addChild($userRole, $child);
		}
		
		// Add back all users
		foreach ($users as $user) {
			$this->auth->assign($userRole, $user);
		}
		//$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		//$this->removeAllPermissions($this->permissions);
    }
}
