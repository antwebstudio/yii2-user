<?php

namespace ant\rbac\views;

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name */


echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class <?= $className ?> extends Migration
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
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
