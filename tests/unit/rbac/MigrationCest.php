<?php
//namespace tests\codeception\ant\rbac;
//use Yii;
use ant\user\rbac\Role;
use ant\rbac\Permission;
use ant\rbac\rules\IsOwnModelRule;
//use tests\codeception\common\UnitTester;
use ant\user\models\User;

class MigrationCest
{
    public function _before(UnitTester $I, $scenario)
    {
		//$scenario->skip('Remove dependency to user module');
		
		Yii::configure(\Yii::$app, [
            'components' => [
				'authManager' => [
					'class' => 'yii\rbac\DbManager',
					'defaultRoles' => [Role::ROLE_GUEST, Role::ROLE_USER],
				],
			],
		]);
		
		Yii::$app->authManager->removeAll();
    }

    public function _after(UnitTester $I)
    {
    }
	
	public function testAddPermissionByName(UnitTester $I) {
		$permissionName = 'testPermission';
		
		// Before add
		$permission = Yii::$app->authManager->getPermission($permissionName);
		$I->assertTrue($permission === null);
		
		// Run migration add permission
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addPermission', [$permissionName]);
		
		// After add
		$permission = Yii::$app->authManager->getPermission($permissionName);
		$I->assertTrue($permission instanceof yii\rbac\Permission);
	}
	
	public function testAddPermissionByCommonPermission(UnitTester $I) {
		$actionPermission = Permission::of(TestMigrationController::class, 'test-action');
		$permissionName = $actionPermission->name;
		
		// Before add
		$permission = Yii::$app->authManager->getPermission($permissionName);
		$I->assertTrue($permission === null);
		
		// Run migration add permission
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addPermission', [$actionPermission]);
		
		// After add
		$permission = Yii::$app->authManager->getPermission($permissionName);
		$I->assertTrue($permission instanceof yii\rbac\Permission);
	}

    // tests
    public function testAddAllPermissions(UnitTester $I)
    {
		$user = User::findOne(1);
		
		$testRole = 'testRole';
		$role = Yii::$app->authManager->createRole($testRole);
		Yii::$app->authManager->add($role);
		
		Yii::$app->authManager->assign($role, $user->id);
		
		$permission = Permission::of('test-action', TestMigrationController::class);
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $role->name));
		
		$permissions = [
			TestMigrationController::class => [
				'test-action' => ['Description', [$testRole]],
			],
		];
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $permission->name));
    }
		
    // tests
    public function testAddAllPermissionsOverwrite(UnitTester $I)
    {
		$user = User::findOne(1);
		$user2 = User::findOne(2);
		$permission = Permission::of('test-action', TestMigrationController::class);
		
		// Create roles
		$testRole = 'testRole';
		$role = $this->addRole($testRole);
		
		$testRole2 = 'testRole2';
		$role2 = $this->addRole($testRole2);
		
		// Assign role
		Yii::$app->authManager->assign($role, $user->id);
		Yii::$app->authManager->assign($role2, $user2->id);
		
		// Make sure role is assigned properly
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $role->name));
		$I->assertTrue(Yii::$app->authManager->checkAccess($user2->id, $role2->name));
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		
		$permissions = [
			TestMigrationController::class => [
				'test-action' => ['Description', [$testRole]],
			],
		];
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		$I->assertFalse(Yii::$app->authManager->checkAccess($user2->id, $permission->name));
		
		// Overwrite permission
		
		$permissions = [
			TestMigrationController::class => [
				'test-action' => ['Description', [$testRole2]],
			],
		];
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		$I->assertTrue(Yii::$app->authManager->checkAccess($user2->id, $permission->name));
    }
	
	public function testAddAllPermissionsOverwriteWithEmptyRoleSet(UnitTester $I)
    {
		$user = User::findOne(1);
		$permission = Permission::of('test-action', TestMigrationController::class);
		
		// Create roles
		$testRole = 'testRole';
		$role = $this->addRole($testRole);
		
		// Assign role
		Yii::$app->authManager->assign($role, $user->id);
		
		// Make sure role is assigned properly
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $role->name));
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		
		$permissions = [
			TestMigrationController::class => [
				'test-action' => ['Description', [$testRole]],
			],
		];
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		
		// Overwrite permission
		
		$permissions = [
			TestMigrationController::class => [
				'test-action' => ['Description', []], // Overwrite with empty role set
			],
		];
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, $permission->name));
    }
	
	public function testAddAllPermissionsWithRuleName(UnitTester $I) {
		$user = User::findOne(1);
		
		$testRole = 'testRole';
		$role = Yii::$app->authManager->createRole($testRole);
		Yii::$app->authManager->add($role);
		Yii::$app->authManager->assign($role, $user->id);
		
		$permission = Permission::of('test-action', TestMigrationModel::class);
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, $permission->name));
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $role->name));
		
		$permissions = [
			TestMigrationModel::class => [
				'test-action' => ['Description', [$testRole], 'ruleName' => IsOwnModelRule::className()],
			],
		];
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, $permission->name, ['model' => new TestMigrationModel]));
		
	}
	
	//////////////////////////////////
	// addChildrenFor
	//////////////////////////////////
	
	public function testAddChildrenFor(UnitTester $I) {
		$user = User::findOne(1);
		
		$testRole = 'testRole';
		$role = Yii::$app->authManager->createRole($testRole);
		Yii::$app->authManager->add($role);
		Yii::$app->authManager->assign($role, $user->id);
		
		$structure = [
			Permission::of('product', TestMigrationModel::class)->name => [
				Permission::of('product/create', TestMigrationModel::class)->name,
				Permission::of('product/update', TestMigrationModel::class)->name,
				Permission::of('product/delete', TestMigrationModel::class)->name,
			],
		];
		
		$permissions = [
			TestMigrationModel::class => ['product' => ['Manage Product', [$testRole]]],
		];
		
		$I->assertFalse(Yii::$app->authManager->checkAccess($user->id, Permission::of('product/update', TestMigrationModel::class)->name));
		
		$migration = new TestMigration;
		$I->invokeMethod($migration, 'addAllPermissions', [$permissions]);
		$I->invokeMethod($migration, 'addChildrenFor', [$structure]);
		
		$I->assertTrue(Yii::$app->authManager->checkAccess($user->id, Permission::of('product/update', TestMigrationModel::class)->name));
	}
	
	protected function addRole($roleName) {
		$role = Yii::$app->authManager->createRole($roleName);
		Yii::$app->authManager->add($role);
		
		return $role;
	}
	
	public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
                'dataFile' => '@tests/fixtures/data/user.php'
            ],
        ];
    }
}

class TestMigrationModel extends \yii\base\Model {
	public $created_by = 1;
}

class TestMigrationController extends \yii\web\Controller {
}

class TestMigration extends \ant\rbac\Migration {
}
