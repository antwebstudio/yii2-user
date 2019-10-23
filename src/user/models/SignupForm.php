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

    protected function removeAppearWantToCutIn($signupType, $fieldsFormBuilderRowsToBeReStructure, $arrayAsNewRow) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-09-04
        if (Yii::$app->getModule('user')->fieldsAppearToCutIn != null && Yii::$app->getModule('user')->fieldsAppearToCutIn[$signupType]) {
            //$ascendingFieldsAppearToCutIn = Yii::$app->getModule('user')->fieldsAppearToCutIn[$signupType];
            if ($arrayAsNewRow) {
                unset($fieldsFormBuilderRowsToBeReStructure[key($fieldsFormBuilderRowsToBeReStructure)]);
            }
        }
        return $fieldsFormBuilderRowsToBeReStructure;
    }
    protected function getAscendingFieldsAppearToCutIn($signupType) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-09-04
        $ascendingFieldsAppearToCutIn = [];
        if (Yii::$app->getModule('user')->fieldsAppearToCutIn != null && Yii::$app->getModule('user')->fieldsAppearToCutIn[$signupType]) {
            $ascendingFieldsAppearToCutIn = Yii::$app->getModule('user')->fieldsAppearToCutIn[$signupType];
            sort($ascendingFieldsAppearToCutIn);
        }
        return $ascendingFieldsAppearToCutIn;
    }

    public function cutArrayAccordingToItsRowSize($fieldsStoredAllFormBuilderRows) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-09-04
        $fieldsFormBuilderRowsToBeReStructure = [];
        foreach ($fieldsStoredAllFormBuilderRows as $key => $fieldStoredFormBuilderRows) {
            $fieldsFormBuilderRowsToBeReStructure[] = array_chunk($fieldStoredFormBuilderRows, $key);
        }
        return $fieldsFormBuilderRowsToBeReStructure;
    }
    public function getFormRows($signupType = 'default'){
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-09-04

        $fieldsStoredAllFormBuilderRows = $this->getFormAttribs($signupType);
        $fieldsFormBuilderRowsToBeReStructure = $this->cutArrayAccordingToItsRowSize($fieldsStoredAllFormBuilderRows);
        $arrayAsNewRow = end($fieldsFormBuilderRowsToBeReStructure);
        $ascendingFieldsAppearToCutIn = $this->getAscendingFieldsAppearToCutIn($signupType);
        $fieldsFormBuilderRowsToBeReStructure = $this->removeAppearWantToCutIn($signupType, $fieldsFormBuilderRowsToBeReStructure, $arrayAsNewRow);
        $fields = $this->strucutureFieldsFormBuilderRowsToBeReStructure($fieldsFormBuilderRowsToBeReStructure);
        $fields = $this->processArrayCutIn($fields, $ascendingFieldsAppearToCutIn, $arrayAsNewRow);
        
        return $fields;
    }

    protected function strucutureFieldsFormBuilderRowsToBeReStructure($fieldsFormBuilderRowsToBeReStructure) {
        $fields = [];
        foreach ($fieldsFormBuilderRowsToBeReStructure as $index_1 => $fieldsFormBuilderRowToBeReStructure){
            foreach ($fieldsFormBuilderRowToBeReStructure as $index_2 => $fieldsToBeReStructure) {
                $tempStructuredFields = [];
                foreach ($fieldsToBeReStructure as $index_3 => $fieldToBeReStructure) {
                    $tempStructuredFields = ArrayHelper::merge($tempStructuredFields, $fieldToBeReStructure);
                }
                $fields[] = $tempStructuredFields;
            }
        }
        return $fields;
    }

    protected function processArrayCutIn($fields, $ascendingFieldsAppearToCutIn, $arrayAsNewRow) {
        $array = [];
        $i = 0;
        foreach ($arrayAsNewRow[0][0] as $key2 => $value2) {
            foreach ($value2 as $key3 => $value3) {
                $array[$i++][]['attributes'][$key3] = $value3;
            }
        }
        if (isset($ascendingFieldsAppearToCutIn) && is_array($ascendingFieldsAppearToCutIn)) {
            foreach ($ascendingFieldsAppearToCutIn as $key => $value) {
                array_splice($fields, $value, 0, $array[0]);
                unset($array[0]);
                if (isset($array)) {
                    $array = array_values($array);
                }
            }
        }
        return $fields;
    }

    protected function getFormAttribs($signupType) {

        $defaultFormBuilderColumnSpan = 2;
        $fieldsStoredAllFormBuilderRows = [];
        foreach (Yii::$app->getModule('user')->signUpModel[$signupType]['fields'] as $key => $value) {
            if (Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['record']['form'] == true) {

                $indexRow = isset(Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['formBuilderOptions']['rows']) ? Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['formBuilderOptions']['rows'] : $defaultFormBuilderColumnSpan;
                if (isset(Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['formBuilderOptions']['rowToAppear']) ? Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['formBuilderOptions']['rowToAppear'] : false) {

                    $fieldsStoredAllFormBuilderRows[999]
                    [
                    Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['formBuilderOptions']['rowToAppear']
                    ]
                    ['attributes'][$key] = Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['field'];
                } else { // not newRow
                    $fieldsStoredAllFormBuilderRows[$indexRow][]['attributes'][$key] = Yii::$app->getModule('user')->signUpModel[$signupType]['fields'][$key]['field'];
                }
            }
        }
        return $fieldsStoredAllFormBuilderRows;
    }

    public function addFields($name){
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // since 2019-09-04
        if ( isset($this->_fields[$name]) ) {
            $this->fields[$name] = $this->_fields[$name];
        }
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
         
}