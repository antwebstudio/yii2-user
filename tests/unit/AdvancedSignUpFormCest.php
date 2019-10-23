<?php
namespace user;

use Yii;
use UnitTester;
use ant\user\models\AdvancedSignupForm;
use ant\user\models\User;
use ant\user\models\UserProfile;
use ant\address\models\Address;
use ant\contact\models\Contact;
use ant\organization\models\Organization;
use ant\organization\models\Company;
use ant\address\models\AddressCountry;

class AdvancedSignupFormCest
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
    public function test(UnitTester $I, $scenario)
    {
		//$scenario->skip();
		
        $params = [
            'AdvancedSignupForm' => [
                'username' => 'chy1988',
                'email' => 'chy1988@gmail.com',
                'password' => '123456',
                'confirmPassword' => '123456',
                'profile' => [],
            ],
        ];
        
        $configs = [
            'default' => [
                'model' => [
                    'class' => AdvancedSignupForm::className(),
                    'usernameLength' => [6, 255],
                    'signupType' => 'default',
                    'needEnterPasswordTwice' => false,
                    'sendActivationEmail' => true,
                    'customFields' => [
                        'profile[firstname]' => [
                            'record' => [
                                'form' => true,
                                'config' => false,
                            ],
                            'field' => [
                                'rules' => [
                                    [['firstname'], 'required'],
                                    [['firstname'], 'string', 'max' => 50]
                                ],
                            ],
                        ],
                    ],
                ],
                'parameter' => [
                    'accountStatus' => \ant\user\models\User::STATUS_NOT_ACTIVATED,
                    'role' => 'user',
                ],
            ],
        ];

        $model = Yii::createObject($configs['default']['model']);
        $model->userIp = '::1';
        $model->load($params);
        $model->signup();

        //throw new \Exception(print_r($model->rules(),1));
        
        $I->assertTrue($model->hasErrors());
        $I->assertTrue(array_key_exists('data.firstname', $model->errors));
    }
	
    public function testSave(UnitTester $I, $scenario)
    {
		//$scenario->skip();
		
		$expectedFirstname = 'firstname';
		
        $params = [
            'AdvancedSignupForm' => [
                'username' => 'chy1988',
                'password' => '123456',
                'confirmPassword' => '123456',
                'profile' => [],
            ],
			'User' => [
                'email' => 'chy1988@gmail.com',
			],
			'UserProfile' => [
				'firstname' => $expectedFirstname,
			],
        ];

        $model = new AdvancedSignupForm;
        $model->userIp = '::1';
		
        $I->assertTrue($model->load($params));
        $I->assertTrue($model->signup());
		$I->assertEquals($expectedFirstname, $model->user->profile->firstname);
    }
	
	public function testSaveAfterGetProfileModel(UnitTester $I, $scenario)
    {
		//$scenario->skip();
		
		$expectedFirstname = 'firstname';
		
        $params = [
            'AdvancedSignupForm' => [
                'username' => 'chy1988',
                'password' => '123456',
                'confirmPassword' => '123456',
                'profile' => [],
            ],
			'User' => [
                'email' => 'chy1988@gmail.com',
			],
			'UserProfile' => [
				'firstname' => $expectedFirstname,
			],
        ];

        $model = new AdvancedSignupForm;
        $model->userIp = '::1';
		
		$model->getModel('profile'); // Fixed bug: This caused sequence of UserProfile model saved after User model, and hence UserProfile not created correctly. This test is to make sure the bug is solved.
		
        $I->assertTrue($model->load($params));
        $I->assertTrue($model->signup());
		$I->assertEquals($expectedFirstname, $model->user->profile->firstname);
    }
	
	public function testSaveComplicated(UnitTester $I) {
		$country = AddressCountry::find()->one();
		$username = 'testuser';
		$email = 'testuser@gmail.com';
		$password = '123456';
		$profile = [
			'firstname' => 'hui yang',
			'lastname' => 'ch\'ng',
			'title' => 'mr.',
			'nationality_id' => $country->id,
		];
		$organizationData = [
			'name' => 'Test Company Name',
			'founded_year' => '1995',
			'registration_number' => 'ABC-123456',
			'website_url' => 'www.antwebstudio.com',
			'data' => ['sst_number' => 'SST-123456'],
		];
		$positionTitle = 'test position';
		$companyContact = [
			'contact_number' => '041234567',
			'fax_number' => '042234567',
		];
		$companyAddress = [
			'address_1' => '1-1-1 street test, location',
			'city' => 'test city',
			'postcode' => '11060',
			'country_id' => $country->id,
		];
		
        $params = [
            (new AdvancedSignupForm)->formName() => [
                'username' => $username,
                'password' => $password,
                'confirmPassword' => $password,
				'positionTitle' => $positionTitle,
            ],
			(new User)->formName() => [
                'email' => $email,
			],
			(new UserProfile)->formName() => $profile,
			(new Organization)->formName() => $organizationData,
			(new Contact)->formName() => $companyContact,
			(new Address)->formName() => $companyAddress,
        ];
        
        $model = Yii::createObject([
			'class' => AdvancedSignupForm::className(),
			'extraModels' => function($formModel) {
				return [
					'companyAddress' => [
						'class' => 'ant\address\models\Address',
						'scenario' => Address::SCENARIO_NO_REQUIRED,
						'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) use ($formModel) {
							$model = $event->sender;
						},
					],
					'companyContact' => [
						'class' => 'ant\contact\models\Contact',
						'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) use ($formModel) {
							$model = $event->sender;
							$model->link('address', $formModel->companyAddress);
						},
					],
					'company' => [
						'class' => 'ant\organization\models\Organization',
						'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) use ($formModel) {
							$model = $event->sender;
							//$model->user_ids = [$formModel->user->id];
							$model->link('users', $formModel->user, ['position_title' => $formModel->positionTitle]);
							$model->link('contact', $formModel->companyContact);
						},
						'as configurable' => [
							'class' => 'ant\behaviors\ConfigurableModelBehavior',
							'extraRules' => [
								[['data'], '\ant\validators\SerializableDataValidator', 'rules' => [
									[['sst_number'], 'required'],
								]],
								[['website_url'], 'required'],
							],
						],
					],
				];
			},
			'usernameLength' => [6, 255],
			/*
			'signupType' => 'default',
			'needEnterPasswordTwice' => false,
			'sendActivationEmail' => true,*/
			/*'customFields' => [
				'profile[firstname]' => [
					'record' => [
						'form' => true,
						'config' => false,
					],
					'field' => [
						'rules' => [
							[['firstname'], 'required'],
							[['firstname'], 'string', 'max' => 50]
						],
					],
				],
			],*/
			/*'as configuratable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],*/
		]);
        /*$configs = [
                'parameter' => [
                    'accountStatus' => \ant\user\models\User::STATUS_NOT_ACTIVATED,
                    'role' => 'user',
                ],
            ],
        ];*/
		$model->sendActivationEmail = false;
        $model->userIp = '::1';
        
		$I->assertTrue($model->load($params));
		
		if (!$model->signup()) throw new \Exception(print_r($model->errors, 1));
		
		//throw new \Exception(Address::findOne($model->companyAddress->id)->address_1.' : '.$model->companyAddress->address_1);
		
        //throw new \Exception(print_r($model->rules(),1));
        
		$user = User::findByUsername($username);
		
		if (!$user->save()) throw new \Exception(print_r($user->errors, 1));
		
		$I->assertTrue(isset($user));
		$I->assertEquals(strtolower($email), $user->defaultEmail);
		$I->assertEquals(strtolower($username), $user->username);
		$I->assertFalse($user->isActive);
		$I->assertTrue($user->validatePassword($password));
		
		$I->assertEquals(User::STATUS_NOT_ACTIVATED, $user->status);
		
		$I->assertFalse(\Yii::$app->authManager->checkAccess($user->id, \ant\rbac\Role::ROLE_USER)); // User is not activated, hence should be false
		
		$user->status = User::STATUS_ACTIVATED; // Needed for checkAccess to be true
		if (!$user->save()) throw new \Exception(print_r($user->errors, 1).print_r($user->attributes, 1));
		
		$I->assertTrue(\Yii::$app->authManager->checkAccess($user->id, \ant\rbac\Role::ROLE_USER));
		
		// Validate profile
		$I->assertEquals($profile['firstname'], $user->profile->firstname);
		$I->assertEquals($profile['lastname'], $user->profile->lastname);
		$I->assertEquals($profile['nationality_id'], $user->profile->nationality_id);
		$I->assertEquals($profile['title'], $user->profile->title);
		
		// Validate organization
		$organization = Organization::find()->joinWith('users users')->andWhere(['users.id' => $user->id])->one();
		$I->assertTrue(isset($organization));
		$I->assertEquals($organizationData['name'], $organization->name);
		$I->assertEquals($organizationData['registration_number'], $organization->registration_number);
		$I->assertEquals($organizationData['founded_year'], $organization->founded_year);
		$I->assertEquals($organizationData['website_url'], $organization->website_url);
		$I->assertEquals($organizationData['data'], $organization->data);
		$I->assertEquals($positionTitle, $organization->userMaps[0]->position_title);
		
		// Validate organization contact
		$I->assertTrue(isset($organization->contact));
		$I->assertEquals($companyContact['contact_number'], $organization->contact->contact_number);
		$I->assertEquals($companyContact['fax_number'], $organization->contact->fax_number);
		
		// Validate organization address
		//throw new \Exception($organization->contact->getAddress()->one()->id);
		//throw new \Exception($organization->contact->address->id.'='.(Address::findOne($organization->contact->address_id)->id));
		//throw new \Exception($model->company->id.' = '.$organization->id.' ; '.$model->company->contact->address->id.' = '.$model->company->contact->address_id. ' = '.$model->companyAddress->id);
		
		/*$contact = Contact::findOne($organization->contact->id);
		
		$address = $contact->getAddress()->one();
		$I->assertTrue(isset($address));
		throw new \Exception($contact->id. ' - '. $address->id .' - '.print_r($contact->address, 1));*/
		
		$I->assertTrue(isset($organization->contact->address));
		$I->assertEquals($companyAddress['address_1'], $organization->contact->address->address_1);
		$I->assertEquals($companyAddress['city'], $organization->contact->address->city);
		$I->assertEquals($companyAddress['postcode'], $organization->contact->address->postcode);
		$I->assertEquals($companyAddress['country_id'], $organization->contact->address->country->id);
		
	}
	
	public function testValidateComplicatedFailed(UnitTester $I, $scenario) {
		//$scenario->skip();
		
		$country = AddressCountry::find()->one();
		$username = '';
		$email = '';
		$password = '';
		$profile = [
		];
		$organizationData = [
			'data' => ['sst_number' => ''],
		];
		$positionTitle = '';
		$companyContact = [
		];
		$companyAddress = [
		];
		
        $params = [
            (new AdvancedSignupForm)->formName() => [
                'username' => $username,
                'password' => $password,
                'confirmPassword' => $password,
				'positionTitle' => $positionTitle,
            ],
			(new User)->formName() => [
                'email' => $email,
			],
			(new UserProfile)->formName() => $profile,
			(new Company)->formName() => $organizationData,
			(new Contact)->formName() => $companyContact,
			(new Address)->formName() => $companyAddress,
        ];
        
        $model = Yii::createObject([
			'class' => AdvancedSignupForm::className(),
			'extraModels' => function($formModel) {
				return [
					'profile' => [
						'class' => UserProfile::className(),
						'on '.\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => function($event) {
							$profile = $event->sender;
							$profile->main_profile = 1;
							$profile->user_id = $this->user->id;
						},
						'as configurable' => [
							'class' => 'ant\behaviors\ConfigurableModelBehavior',
							'extraRules' => [
								[['firstname', 'lastname'], 'required'],
							],
						],
					],
					'companyAddress' => [
						'class' => 'ant\address\models\Address',
						'scenario' => Address::SCENARIO_NO_REQUIRED,
						'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) use ($formModel) {
							$model = $event->sender;
						},
					],
					'companyContact' => [
						'class' => Contact::className(),
						'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) use ($formModel) {
							$model = $event->sender;
							$model->link('address', $formModel->companyAddress);
						},
					],
					'company' => [
						'class' => 'ant\organization\models\Company',
						'scenario' => Organization::SCENARIO_ALL_REQUIRED,
						'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) use ($formModel) {
							$model = $event->sender;
							//$model->user_ids = [$formModel->user->id];
							$model->link('users', $formModel->user, ['position_title' => $formModel->positionTitle]);
							$model->link('contact', $formModel->companyContact);
						},
						'as configurable' => [
							'class' => 'ant\behaviors\ConfigurableModelBehavior',
							'extraRules' => [
								[['data'], '\ant\validators\SerializableDataValidator', 'rules' => [
									[['sst_number'], 'required'],
								]],
								[['website_url'], 'required'],
							],
						],
					],
				];
			},
			'usernameLength' => [6, 255],
		]);
		
		$model->sendActivationEmail = false;
        
		$I->assertTrue($model->load($params));
		$I->assertFalse($model->signup());
		
		//throw new \Exception(print_r($model->errors,1));
		$I->assertEquals([
			'username' => ['Username cannot be blank.'],
			'password' => ['Password cannot be blank.'],
			'confirmPassword' => ['Confirm Password cannot be blank.'],
			'user' => [['Username cannot be blank.'], ['Email cannot be blank.']],
			'profile' => [['First Name cannot be blank.'], ['Last Name cannot be blank.']],
			'company' => [['Company Name cannot be blank.'], ['Founded Year cannot be blank.'], ['Sst Number cannot be blank.'], ['Website Url cannot be blank.']],
		], $model->errors);
		
		
	}
}
