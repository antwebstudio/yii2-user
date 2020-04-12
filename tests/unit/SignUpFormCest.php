<?php
namespace user;

use Yii;
use UnitTester;
use ant\rbac\Role;
use ant\user\models\SignUpForm;
use ant\user\models\User;

class SignUpFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
            ],
            'userProfile' => [
                'class' => \tests\fixtures\UserProfileFixture::className(),
            ],
        ];
    }

    // tests
    public function testSignupSuccess(UnitTester $I)
    {
		$username = 'Chy1988';
		$email = 'Test@gmail.com'; // To test if after signup, the email store should be all lowered case
		$password = '123456';
		
        $model = new SignupForm([
			'on '.\ant\user\models\SignupForm::EVENT_AFTER_SIGNUP => function($event) {
				$model = $event->sender;
				//$model->sendActivationEmail();
				$notification = new \ant\user\notifications\Activation($model->user);
				\Yii::$app->notifier->send($model->user, $notification);
				\Yii::$app->user->login($model->user);
			}
		]);
        $data = [
            (new User)->formName() => [
                'email' => $email,
                'profile' => [],
            ],
			(new SignupForm)->formName() => [
                'username' => $username,
                'password' => $password,
                'confirmPassword' => $password,
			],
        ];
		
		//$model->sendActivationEmail = false;
		$model->userIp = '::1';
		
        $I->assertTrue($model->load($data));
        $model->signup();
		
		if ($model->hasErrors()) throw new \Exception(print_r($model->errors,1));
        $I->assertFalse($model->hasErrors());
		
		$user = User::findByUsername($username);
		
		$I->assertTrue(isset($user));
		$I->assertEquals(strtolower($email), $user->defaultEmail);
		$I->assertEquals(strtolower($username), $user->username);
		$I->assertFalse($user->isActive);
		$I->assertTrue($user->validatePassword($password));
		
		$I->assertEquals(User::STATUS_NOT_ACTIVATED, $user->status);
		
		$manager = Yii::$app->authManager;
		$item = $manager->getRole(Role::ROLE_USER);
		
		$I->assertFalse(\Yii::$app->authManager->checkAccess($user->id, Role::ROLE_USER)); // User is not activated, hence should be false
		
		$user->status = User::STATUS_ACTIVATED; // Needed for checkAccess to be true
		$user->save();
		
		$I->assertTrue(\Yii::$app->authManager->checkAccess($user->id, Role::ROLE_USER));
		
		$I->seeEmailIsSent();
    }
	
	public function testUpdateUserAfterSignup() {
        $params = [
            'SignupForm' => [
                'username' => 'chy1988',
                'password' => '123456',
                'confirmPassword' => '123456',
                'profile' => [],
            ],
			'User' => [
                'email' => 'chy1988@gmail.com',
			],
			'UserProfile' => [
				'firstname' => 'firstname',
			],
        ];

        $model = new \ant\user\models\SignupForm;
        $model->userIp = '::1';
		$model->load($params);
		if (!$model->signup()) throw new \Exception(print_r($model->errors, 1));
		
		$user = User::findOne($model->user->id);
		if (!$user->save()) throw new \Exception(print_r($user->errors, 1));
	}
	
	public function testDefaultUserStatus(UnitTester $I) {
		$expectedStatus = User::STATUS_ACTIVATED;
		$username = 'testusername';
		$password = '123456';
		
        $model = new SignUpForm(['defaultUserStatus' => $expectedStatus]);
        $data = [
            (new User)->formName() => [
                'email' => 'test@gmail.com',
                'profile' => [],
            ],
			(new SignUpForm)->formName() => [
                'username' => $username,
                'password' => $password,
                'confirmPassword' => $password,
			],
        ];
		
		//$model->sendActivationEmail = false;
		$model->userIp = '::1';
		
        $model->load($data);
        $model->signup();
		
		$user = User::findByUsername($username);
		
		$I->assertTrue(isset($user));
		$I->assertEquals($expectedStatus, $user->status);
	}
	
	public function testSignupUsernameInvalid(UnitTester $I) {
		$username = '88chy1988';
		$password = '123456';
		
        $model = new SignUpForm;
        $data = [
            (new User)->formName() => [
                'email' => 'test@gmail.com',
                'profile' => [],
            ],
			(new SignUpForm)->formName() => [
                'username' => $username,
                'password' => $password,
                'confirmPassword' => $password,
			],
        ];
		
		$model->sendActivationEmail = false;
		$model->userIp = '::1';
		
        $I->assertTrue($model->load($data));
        $model->signup();
		
        $I->assertTrue($model->hasErrors());
		
		$user = User::findByUsername($username);
		$I->assertFalse(isset($user));
	}
}
