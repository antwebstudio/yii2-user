<?php

namespace ant\collaborator\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Role;
use ant\rbac\Migration;

class M181006095315_permissions extends Migration
{
    protected $permissions;
	
	public function init() {
		$this->permissions = [
			\frontend\modules\collaborator\controllers\CollaboratorGroupController::className() => [
				'manage' => ['Manage collaborator group', [Role::ROLE_USER]],
				'delete-collaborator' => ['Delete collaborator group map', [Role::ROLE_USER]],
				'ajax-users' => ['Load user list for manage collaborator group', [Role::ROLE_USER]],
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
