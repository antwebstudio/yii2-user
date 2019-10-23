<?php
namespace user;
use \UnitTester;
use ant\user\rbac\Role;
use ant\user\models\User;
use ant\user\models\UserInvite;
use ant\user\models\UserProfile;
use ant\user\models\UserConfig;
use ant\user\models\InvitedUserSignupForm;

class InvitedUserSignupFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSave(UnitTester $I)
    {
		$email = 'test@example.com';
		$invite = $this->createInvite($email);
		
		$model = new InvitedUserSignupForm([
			'skipTokenValidation' => true,
			'tokenkey' => 'xxx',
			'email' => $email,
			'roleName' => $invite->role,
		]);
		
		$model->load([
			'InvitedUserSignupForm' => [
				'username' => 'testuser',
				'password' => '12345678',
				'confirmPassword' => '12345678',
			],
			(new User)->formName() => [
				'email' => $email,
			],
		]);
		
		if (!$model->signup()) throw new \Exception(print_r($model->errors, 1));
		
		$user = User::findOne($model->user->id);
		
		$I->assertEquals($email, $user->email);
		$I->assertTrue($user->isActive);
		
		$invite = UserInvite::findOne($invite->id);

		// Assert if the user invite is recorded as completed
		$I->assertEquals(UserInvite::STATUS_ACTIVATED, $invite->status);
		$I->assertEquals($user->id, $invite->user_id);
    }
	
	public function testValidateTokenWithInvalidToken(UnitTester $I) {
		
		$email = 'test@example.com';
		$model = $this->createInvite($email);
		
		$model = new InvitedUserSignupForm([
			'tokenkey' => 'xxx',
			'email' => $email,
		]);
		
		$I->assertFalse($model->validateToken());
	}
	
    public function testInvalidToken(UnitTester $I)
    {
		$email = 'test@example.com';
		$model = $this->createInvite($email);
		$exceptionThrown = false;
		
		try {
			$model = new InvitedUserSignupForm([
				'tokenkey' => 'xxx',
				'email' => $email,
			]);
			
			$model->signUp();
		} catch (\Exception $ex) {
			$exceptionThrown = true;
		}
		
		$I->assertTrue($exceptionThrown);
    }
	
    public function testSaveWithProfileData(UnitTester $I)
    {
		$email = 'test@example.com';
		$data = [
			'profile' => [
				'firstname' => 'test firstname',
				'lastname' => 'test lastname',
			],
		];
		$invite = $this->createInvite($email, $data);
		
		$model = new InvitedUserSignupForm([
			'skipTokenValidation' => true,
			'tokenkey' => 'xxx',
			'email' => $email,
			'roleName' => $invite->role,
		]);
		
		$model->load([
			'InvitedUserSignupForm' => [
				'username' => 'testuser',
				'password' => '12345678',
				'confirmPassword' => '12345678',
			],
			(new User)->formName() => [
				'email' => $email,
			],
		]);
		
		if (!$model->signup()) throw new \Exception(print_r($model->errors, 1));
		
		$user = User::findOne($model->user->id);
		
		$I->assertEquals($email, $user->email);
		$I->assertEquals($data['profile']['firstname'], $user->profile->firstname);
		$I->assertEquals($data['profile']['lastname'], $user->profile->lastname);
    }
	
	public function testSaveWithUserConfig(UnitTester $I)
    {
		$email = 'test@example.com';
		$data = [
			'userConfig' => [
				'currency' => 'MYR',
				'discount' => '10',
			],
		];
		$invite = $this->createInvite($email, $data);
		
		$model = new InvitedUserSignupForm([
			'skipTokenValidation' => true,
			'tokenkey' => 'xxx',
			'email' => $email,
			'roleName' => $invite->role,
		]);
		
		$model->load([
			'InvitedUserSignupForm' => [
				'username' => 'testuser',
				'password' => '12345678',
				'confirmPassword' => '12345678',
			],
			(new User)->formName() => [
				'email' => $email,
			],
		]);
		
		if (!$model->signup()) throw new \Exception(print_r($model->errors, 1));
		
		$I->assertEquals($data['userConfig']['currency'], UserConfig::get($model->user->id, 'currency'));
		$I->assertEquals($data['userConfig']['discount'], UserConfig::get($model->user->id, 'discount'));
    }
	
	public function testAutoFill(UnitTester $I)
    {
		$email = 'test@example.com';
		$data = [
			'profile' => [
				'firstname' => 'test firstname',
				'lastname' => 'test lastname',
			],
			'userConfig' => [
				'currency' => 'MYR',
				'discount' => '10',
			],
		];
		$invite = $this->createInvite($email, $data);
		
		$model = new InvitedUserSignupForm([
			'skipTokenValidation' => true,
			'tokenkey' => 'xxx',
			'email' => $email,
			'roleName' => $invite->role,
		]);
		
		$model->load(null);
		
		$I->assertEquals($email, $model->email);
		$I->assertEquals($email, $model->getModel('user')->email);
		$I->assertEquals($data['profile']['firstname'], $model->getModel('profile')->firstname);
		$I->assertEquals($data['profile']['lastname'], $model->getModel('profile')->lastname);
		$I->assertEquals($data['userConfig']['currency'], $model->userConfig['currency']);
		$I->assertEquals($data['userConfig']['discount'], $model->userConfig['discount']);
    }
	
	public function testAutoFillShouldNotOverwriteLoad(UnitTester $I) {
		
		$email = 'test@example.com';
		$data = [
			'profile' => [
				'firstname' => 'test firstname',
				'lastname' => 'test lastname',
			],
			'userConfig' => [
				'currency' => 'MYR',
				'discount' => '10',
			],
		];
		$newEmail = 'new'.$email;
		$newData = [
			'profile' => [
				'firstname' => 'new firstname',
				'lastname' => 'new lastname',
			],
			'userConfig' => [
				'currency' => '',
				'discount' => '20',
			],
		];
		$invite = $this->createInvite($email, $data);
		
		$model = new InvitedUserSignupForm([
			'skipTokenValidation' => true,
			'tokenkey' => 'xxx',
			'email' => $email,
			'roleName' => $invite->role,
		]);
		
		$model->load([
			'InvitedUserSignupForm' => [
				'userConfig' => [
					'currency' => $newData['userConfig']['currency'],
					'discount' => $newData['userConfig']['discount'],
				],
			],
			(new UserProfile)->formName() => [
				'firstname' => $newData['profile']['firstname'],
				'lastname' => $newData['profile']['lastname'],
			],
			(new User)->formName() => [
				'email' => $newEmail,
			],
		]);
		
		$I->assertEquals($newEmail, $model->getModel('user')->email);
		$I->assertEquals($newData['profile']['firstname'], $model->getModel('profile')->firstname);
		$I->assertEquals($newData['profile']['lastname'], $model->getModel('profile')->lastname);
		$I->assertEquals($newData['userConfig']['discount'], $model->userConfig['discount']);
		$I->assertEquals($newData['userConfig']['currency'], $model->userConfig['currency']);
	}
	
	protected function createInvite($email, $data = []) {
		$model = new UserInvite(['emailFrom' => 'noreply@example.com']);
		$model->attributes = [	
			'email' => $email,
			'role' => Role::ROLE_USER,
			'data' => $data,
		];
		if (!$model->sendInvite()) throw new \Exception(print_r($model->errors, 1));
		
		return $model;
	}
	
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
                'dataFile' => '@tests/fixtures/data/user.php'
            ],
            'userProfile' => [
                'class' => \tests\fixtures\UserProfileFixture::className(),
                'dataFile' => '@tests/fixtures/data/user_profile.php'
            ],
            'userInvite' => [
                'class' => \tests\fixtures\UserInviteFixture::className(),
                'dataFile' => '@tests/fixtures/data/user_invite.php'
            ],
        ];
    }
}
