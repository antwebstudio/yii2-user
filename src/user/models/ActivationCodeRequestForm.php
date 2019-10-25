<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use ant\helpers\Mail;
use ant\helpers\ArrayHelper;
use ant\commands\SendEmailCommand;
use ant\user\models\User;
use ant\rbac\components\Role;
use ant\token\models\Token;

/**
 * Signup form
 */
class ActivationCodeRequestForm extends Model
{
    const ACTIVATION_CODE_LENGTH = 8;

	public $user; // Logged in user
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['!user'], 'required', 'when' => function() {
				return !Yii::$app->user->isGuest;
			}],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'exist',
            'targetClass' => '\ant\user\models\User',
            'filter' => ['status' => User::STATUS_NOT_ACTIVATED ] ,
                'message' => 'There is no inactivate user with such email.' 
            ],
        ];
    }

    /**
     * Send activation code to user via email.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function send()
    {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // Added 2019-10-04
		
        if (!$this->validate()) return false;

        if (isset($this->user)) {
			// Update email to latest email
			$this->user->email = $this->email;
			if (!$this->user->save()) throw new \Exception('Failed to update user. '.print_r($this->user->errors, 1));
		} else {
			$this->user = User::findByEmail($this->email); // No need to check existence of user as should be already check in validation.
		}
		
		$file = '@ant/user/mail/accountActivation';
		$file = file_exists(Yii::getAlias($file).'.php') ? $file : 'accountActivation';
		
		return Yii::$app->mailer->compose($file, $this->getMailParams())
			->setFrom(Mail::getDefaultFrom())
			->setTo($this->email)
			->setSubject('Account activation for ' . Yii::$app->name)
			->send();
    }

    public static function createToken($user) {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // Added 2019-10-04
		
		$tokenIds = Token::find()
			->alias('token')
			->select('token.id')
			->byUser($user)
			->byType(Token::TOKEN_TYPE_USER_ACTIVATION)
			->asArray()->all();
			
		//foreach ($previousTokens as $token) $token->delete();
		Token::deleteAll(['id' => ArrayHelper::getValues($tokenIds, 'id')]);

		return Token::create($user, Token::TOKEN_TYPE_USER_ACTIVATION, [
			'code' => self::generateActivationCode(),
			'email' => $user->email,
		]);
    }

    /**
     * Sends an email with a link, for activate the account.
     *
     * @return boolean whether the email was send
     */
    public static function sendActivationEmail($user)
    {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // Added 2019-10-04
		
		$model = new self;
		$model->user = $user;
		$model->email = $user->email;
		
		return $model->send();
    }

    /**
     * Generate random activation code
     *
     * @return string
     */
    public static function generateActivationCode(){
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // Added 2019-10-04
		
        return Yii::$app->security->generateRandomString(self::ACTIVATION_CODE_LENGTH);
    }
	
	protected function getMailParams() {
		$user = isset($this->user) ? $this->user : User::findByEmail($this->email);
		$token = self::createToken($user);
		
		return [
			'user' => $user,
			'activationLink' => Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(ArrayHelper::merge(['user/signin/token-activation'], $token->queryParams)),
			'activationCode' => $token->queryParams['code'],
		];
	}
}
