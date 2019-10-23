<?php
//namespace tests\codeception\common\user;
//use tests\UnitTester;
use yii\helpers\Html;
use ant\user\rbac\Role;
use ant\user\models\User;
use ant\user\models\UserProfile;
use ant\user\models\SignupForm;

class UserCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
	// 
	public function testRulesForScenarioEmailAsUsername(UnitTester $I) {
		$model = new User(['scenario' => User::SCENARIO_EMAIL_AS_USERNAME]);
		$I->assertFalse($model->validate());
	}
	
	public function testSaveUpdate(UnitTester $I) {
		$model = new User();
		$model->attributes = [
			'username' => 'testsignup',
			'email' => 'test@gmail.com',
			'status' => User::STATUS_NOT_ACTIVATED,
			'auth_key',
		];
		$model->registered_ip = '127.0.0.1';
		$model->setPassword('12345678');
        $model->generateAuthKey();
		
		$I->assertTrue($model->save());
		
		$model->status = User::STATUS_ACTIVATED;
		$I->assertTrue($model->save());
	}
	
	public function testValidateUsername(UnitTester $I) {
		$validUsername = [
			'testusername',
			'Testusername',
			'username_test',
			'test@example.com',
		];
		$invalidUsername = [
			'test username',
			'123username',
			'172938478917234',
			'username@',
			'user-name',
		];
		
		$model = new User();
		$model->attributes = [
			'username' => 'testsignup',
			'email' => 'test@gmail.com',
			'status' => User::STATUS_NOT_ACTIVATED,
			'auth_key',
		];
		$model->registered_ip = '127.0.0.1';
		$model->setPassword('12345678');
        $model->generateAuthKey();
		
		foreach ($validUsername as $username) {
			$model->username = $username;
			$I->assertTrue($model->validate());
		}
		
		foreach ($invalidUsername as $username) {
			$model->username = $username;
			$I->assertFalse($model->validate(), '"'.$username.'" is invalid username.');
		}
	}
	
	public function testValidateFailedEmailExisted(UnitTester $I) {
		$email = 'user@example.org'; // Existed, inserted from UserFixture
		
		$model = new User();
		$model->attributes = [
			'username' => 'testsignup',
			'email' => $email,
			'status' => User::STATUS_NOT_ACTIVATED,
			'auth_key',
		];
		$model->registered_ip = '127.0.0.1';
		$model->setPassword('12345678');
        $model->generateAuthKey();
		
		$I->assertFalse($model->validate());
		$I->assertEquals(['email' => ['This email address has already been taken.']], $model->errors);
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
