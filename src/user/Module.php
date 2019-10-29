<?php

namespace ant\user;
use yii\helpers\ArrayHelper;
use ant\user\models\UserProfile;

/**
 * user module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
	
    const MENU_PROFILE = 'profile';
    const MENU_VIEW_PROFILE = 'view-profile';
	
	public $signupNeedAdminApproval = false;
	
	/*public $usernameLength = [6, 255];
    public $profileFields = null;

    //user profile config
    public $profileAddressFields = true;
    public $profileUpdateScenario = 'default';
    public $profileDefaultUserUsing = 'user';
    public $profileDefaultEditingUser = 'user';*/
    
    /*public $signUpModel = [
        'default' => [
            'model' => [
                'class' => 'ant\user\models\AdvancedSignUpForm',
                'usernameLength' => [6, 255],
                'signupType' => 'advanced',
            ],
            'parameter' => [
                'accountStatus' => \ant\user\models\User::STATUS_NOT_ACTIVATED,
                'role' => \ant\rbac\Role::ROLE_USER,
            ],
        ],
        'invite' => [
            'model' => [
                'class' => 'ant\user\models\CreateInviteForm',
                'usernameLength' => [6, 255],
                'signupType' => 'invite',
            ],
            'parameter' => [
                'accountStatus' => \ant\user\models\User::STATUS_ACTIVATED,
                'role' => \ant\rbac\Role::ROLE_USER,
                'showInPreSignup' => false,
            ],
        ]
    ];

    public $userConfigForm = [];*/
	
	public function formModels() {
		return [
			'createInvite' => [
				'class' => 'ant\user\models\CreateUserInviteForm',
			],
			'profile' => [
				'class' => 'ant\user\models\ProfileForm',
				/*'as field' => [
					'class' => 'ant\behaviors\ConfigurableModelBehavior',
				],*/
			],
			'contact' => [
				'class' => 'ant\address\models\Address',
				'scenario' => \ant\address\models\Address::SCENARIO_CUSTOM_STATE,
				
			],
			'identity' => [
				'class' => 'ant\user\models\UserIdentityForm',
			],
			'config' => [
				'class' => 'ant\user\models\UserConfigForm',
				
			],
			'signup' => [
				'class' => 'ant\user\models\SignupForm',
				'on '.\ant\user\models\SignupForm::EVENT_AFTER_SIGNUP => function($event) {
					$model = $event->sender;
					$notification = new \ant\user\notifications\Activation($model->user);
					\Yii::$app->notifier->send($model->user, $notification);
					\Yii::$app->user->login($model->user);
				}
			],
			'signupInvitedUser' => [
				'class' => 'ant\user\models\InvitedUserSignupForm',
				'on '.\ant\user\models\SignupForm::EVENT_AFTER_SIGNUP => function($event) {
					$model = $event->sender;
					$model->sendActivationEmail();
					\Yii::$app->user->login($model->user);
				},
				'extraModels' => function($formModel) {
					return [
						'profile' => [
							'class' => 'ant\user\models\UserProfile',		
							'on '.\ant\user\models\UserProfile::EVENT_BEFORE_INSERT => function($event) use ($formModel) {
								$profile = $event->sender;
								$profile->main_profile = 1;
								$profile->user_id = $formModel->user->id;
							},
							'as configurable' => [
								'class' => 'ant\behaviors\ConfigurableModelBehavior',
								'extraRules' => [
									[['firstname', 'lastname', 'company', 'contact'], 'required'],
								],
							],
						]
					];
				},						
			],
			'login' => [
				'class' => 'ant\user\models\LoginForm',
			],
		];
	}
	
	public function behaviors() {
		return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModuleBehavior',
			]
		];
	}

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
    
	/*
    public function getDefaultProfileFields() {
        return [
            'default' => [ //model type
                'model' => [
                    'class' => 'ant\user\models\UserProfile',
                ],
                'user' => [ //userType
                    'user' => [ // editting user type
                        'fields' => [
                            'profile' => [
                                'picture' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'widget',
                                        'widgetClass' => \trntv\filekit\widget\Upload::className(),
                                        'options' => [
                                            'url' => [
                                                'avatar-upload'
                                            ]
                                        // 'rules' => [
                                            
                                        // ],
                                        ],
                                    ],
                                ],
                                'firstname' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'lastname' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],                            
                                'company' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'gender' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'dropdownList',
                                        'items' => [
                                            0 => 'Select ...',
                                            UserProfile::GENDER_MALE => 'Male',
                                            UserProfile::GENDER_FEMALE => 'Female',
                                        ],
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'contact' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                            ]
                        ],
                    ],
                ],
                'company' => [
                    'company' => [ // editting user type
                        'fields' => [
                            'profile' => [
                                'picture' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'widget',
                                        'widgetClass' => \trntv\filekit\widget\Upload::className(),
                                        'options' => [
                                            'url' => [
                                                'avatar-upload'
                                            ]
                                        // 'rules' => [
                                            
                                        // ],
                                        ],
                                    ],
                                ],
                                'firstname' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'lastname' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],                            
                                'company' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'gender' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'dropdownList',
                                        'items' => [
                                            0 => 'Select ...',
                                            UserProfile::GENDER_MALE => 'Male',
                                            UserProfile::GENDER_FEMALE => 'Female',
                                        ],
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'contact' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                    ],
                                ],
                                'data[fax]' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                        'label' => 'Registration NO.',
                                    ],
                                ],
                                'data[fax]' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                        'label' => 'Fax',
                                    ],
                                ],
                                'data[position]' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                        'label' => 'Position',
                                    ],
                                ],
                            ],
                            'address' => [
                                'address_1' => [
                                    'field' => [
                                        'next' => true,
                                        'type' => 'textInput',
                                        // 'rules' => [
                                            
                                        // ],
                                        'label' => 'Company Address',
                                    ],
                                ],
                            ]
                        ],
                    ],
                ],
            ]
        ];
    }*/
}
