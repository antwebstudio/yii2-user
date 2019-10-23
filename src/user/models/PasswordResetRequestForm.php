<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;

use ant\user\models\User;
use ant\token\models\Token;
use ant\commands\SendEmailCommand;
/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    /**
     * reset key length
     */
    const RESET_KEY_LENGTH = 40;

    /**
     * @var user email
     */
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\ant\user\models\User',
                'filter' => [ 'or' , ['status' => User::STATUS_ACTIVATED] , ['status' => User::STATUS_NOT_ACTIVATED ] ],
                'message' => 'There is no user with such email.' 
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::find()
            //modify for password reset does not care active or no active
            //->active()
            ->andWhere(['email' => $this->email])
            ->one();

        if ($user) {
            $key = $this->generatePasswordResetKey();

            $tokens = Token::find()
                ->byUser($user)
                ->byType(Token::TOKEN_TYPE_USER_PASSWORD_RESET)
                ->all();
            foreach($tokens as $token) $token->delete();

            $token = Token::create($user, Token::TOKEN_TYPE_USER_PASSWORD_RESET,  [
                'email' => $user->email,
                'tokenkey' => Token::createTokenKey()
            ]);

            return Yii::$app->mailer->compose(
                    ['text' => 'passwordResetToken-text', 'html' => 'passwordResetToken-html'
                    ],
                    [                        'user' => $user,
                        'tokenQueryParams' => $token->queryParams
                    ])
                    ->setFrom([$this->emailFrom => Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Password reset for ' . Yii::$app->name)
                    ->send();
        }

        return false;
    }

	public function getEmailFrom() {
		return function_exists('env') ? env('ROBOT_EMAIL') : 'robot@antwebstudio.com';
	}

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email'=> 'E-mail'
        ];
    }

    /**
     * Generate random activation code
     *
     * @return string
     */
    public static function generatePasswordResetKey(){
        return Yii::$app->security->generateRandomString(self::RESET_KEY_LENGTH);
    }
}
