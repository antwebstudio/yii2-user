<?php
namespace ant\user\models;

use Yii;
use yii\db\ActiveRecord;
use ant\helpers\StringHelper;
/*use yii\base\Model;
use ant\commands\SendEmailCommand;

use ant\user\models\User;
use ant\user\models\ActivationCodeRequestForm;
use ant\rbac\Role;
use ant\user\models\UserProfile;
use kartik\builder\Form;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;*/

class CreateUserForm extends \ant\base\FormModel
{
    public $username;
    public $email;
    
    public $password;

    public $queueEmail = false;

    /*public $confirmPassword;
    public $userIp;
    public $usernameLength = [6, 20];*/
    /*public $sendActivationEmail = true;
	public $signupNeedAdminApproval = false;
    public $signupType;*/
 
    /*public $fields = ['username' , 'email' , 'password' , 'confirmPassword' ];

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
    ];*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
        ];
    }

    public function configs() {
        return [
            'user' => [
                'class' => User::className(),
                'scenario' => User::SCENARIO_EMAIL_AS_USERNAME,
                'on '.ActiveRecord::EVENT_BEFORE_VALIDATE => function($event) {
                    $user = $event->sender;
                    $user->username = $this->email;
                    $user->email = $this->email;
                    $user->generateAuthKey();
                    
                    $this->password = StringHelper::generateRandomString(8);
                    $user->setPassword($this->password);
                }
            ]
        ];
    }
	
	public function getFormAttributes($name = null) {
		return [
			'email' => [
				'attribute' => 'email',
			],
		];
	}

    // This is needed for importer module
    public function getPrimaryKey() {
        return $this->user->primaryKey;
    }

    // User should set their password when activate
    public function sendAccountActivationEmail() {
        $user = $this->user;
        $to = $user->email;
        $token = \ant\user\models\ActivationCodeRequestForm::createToken($user);
        
        $message = \Yii::$app->mailer->compose('user/auto-signup', [
            'user' => $user,
            'token' => $token,
        ])
            ->setFrom([env('ROBOT_EMAIL') => \Yii::$app->name])
            ->setTo(YII_DEBUG ? env('DEVELOPER_EMAIL') : $to)
            ->setSubject('Welcome to '.\Yii::$app->name);
        
        if ($this->queueEmail) {
            return $message->queue();
        } else {
            return $message->send();
        }
    }
}