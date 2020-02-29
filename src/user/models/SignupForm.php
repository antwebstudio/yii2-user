<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use kartik\builder\Form;
use ant\commands\SendEmailCommand;
use ant\user\models\User;
use ant\user\models\ActivationCodeRequestForm;
use ant\user\rbac\Role;
use ant\user\models\UserProfile;

class SignupForm extends \ant\base\FormModel
{
	const EVENT_AFTER_SIGNUP = 'after_signup';

    const SCENARIO_DEFAULT = 'default';
	const SCENARIO_BACKEND = 'backend';
    const SCENARIO_INVITE_USER = 'inviteUser';
	
    public $username;
    public $email;
    public $password;
    public $confirmPassword;
    public $userIp;
    public $usernameLength = [6, 20];
    public $sendActivationEmail = true;
	public $allowEmailAsUsername = false;
    public $signupType;
	public $roleName = Role::ROLE_USER;
	public $defaultUserStatus = User::STATUS_NOT_ACTIVATED;
 
    public $fields = ['username' , 'email' , 'password' , 'confirmPassword'];
	
	protected $_user;

    private $_fields = [
        'username' => [
            'type' => Form::INPUT_TEXT,
            'options' => ['placeholder' => 'Username'],
            'label' => '<div class="label-signup">Username</div>',
        ],
        'email' => [
            'type' => Form::INPUT_TEXT,
            'options' => ['placeholder' => 'xxx@xxxx.com'],
            'label' => '<div class="label-signup">Email</div>',
        ],
        'password' => [
            'type' => Form::INPUT_PASSWORD,
            'options' => ['placeholder' => 'Password'],
            'label' => '<div class="label-signup">Password</div>',
        ],
        'confirmPassword' => [
            'type' => Form::INPUT_PASSWORD,
            'options' => ['placeholder' => 'Re-enter Password'],
            'label' => '<div class="label-signup">Confirm Password</div>',
        ],
    ];

	public function setSignupNeedAdminApproval($value) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-09-04
	}
	
	public function behaviors() {
		return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
			[
				'class' => 'ant\behaviors\EventHandlerBehavior',
				'events' => [
					self::EVENT_BEFORE_COMMIT_SAVE => function($event) {
						$user = $event->sender->user;
						UserProfile::ensureExist($user->id);
					}
				],
			],
		];
	}
	
	public function models() {
		return [
			'user' => [
				'class' => \ant\user\models\User::className(),
				//'scenario' => \ant\contact\models\Contact::SCENARIO_BASIC_REQUIRED,
				//'name' => 'billTo',
				'on '.ActiveRecord::EVENT_BEFORE_VALIDATE => function($event) {
					$user = $event->sender;
					
					$user->username = $this->username;
					$user->setPassword($this->password);
					$user->status = $this->defaultUserStatus;
					$user->generateAuthKey();
				},
				'on '.ActiveRecord::EVENT_AFTER_INSERT => function($event) {
					$user = $event->sender;
					
					\Yii::$app->authManager->revokeAll($user->id);
		
					// Assign new role for new user
					$role = Role::ensureUserRole($this->roleName);
					$role->assign($user);
				},
			],
		];
	}
    public function init(){
        parent::init();
        foreach ($this->fields as $field => $name) {
            $this->fields[$name] = $this->_fields[$name];
        }
		//$this->on(self::EVENT_AFTER_SIGNUP, [$this, 'afterSignUp']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
		$usernamePatternRule = $this->allowEmailAsUsername ? [['username'], 'email'] : [['username'], 'match', 'pattern' => '/^[a-z]\w*$/i'];
		
        return $this->getCombinedRules([
            ['username', 'trim'],
            [['username', '!usernameLength'], 'required'],
			[['username', 'email'], 'filter', 'filter' => 'strtolower'],
			
            $usernamePatternRule,
			
            ['username', 'unique', 'targetClass' => '\ant\user\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => $this->usernameLength[0], 'max' => $this->usernameLength[1]],['email', 'trim'],
            /*['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\ant\user\models\User', 'message' => 'This email address has already been taken.'],
            ['email', 'trim'],*/
            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['confirmPassword', 'required'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Password not match'],
        ]);
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup($role = Role::ROLE_USER, $status = User::STATUS_NOT_ACTIVATED, $email = null)
    {
		if ($success = $this->save()) {
			$this->trigger(self::EVENT_AFTER_SIGNUP);
		}
		return $success;
    }
	
	public function sendActivationEmail() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-11-05
		ActivationCodeRequestForm::sendActivationEmail($this->user);
	}

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username'=> 'Username',
            'email'=> 'E-mail',
            'password'=> 'Password',
            'confirmPassword'=> 'Confirm Password',
            //'firstname' =>' First Name',
            //'lastname' => 'Last Name',
		]; 
    }
	
	public function getAttributeLabel($attribute) {
		return \Yii::t('user', parent::getAttributeLabel($attribute));
	}
         
}