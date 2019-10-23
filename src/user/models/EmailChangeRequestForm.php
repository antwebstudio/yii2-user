<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use cheatsheet\Time;

use ant\user\models\User;
use ant\token\models\Token;
use ant\commands\SendEmailCommand;
/**
 * Password reset request form
 */
class EmailChangeRequestForm extends Model
{
    const EMAIL_CHANGE_KEY_LENGTH = 40;
    /**
     * @var user email
     */
    public $email;
	
	public function init() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\ant\user\models\User', 'message' => 'This email has already been taken.'],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
        /* @var $user User */
        $user = $this->getUser();

        if ($user) {
            $key = $this->generateEmailChangeKey();

            //remove previous reques
            $tokens = Token::find()
                ->byUser($user)
                ->byType(Token::TOKEN_TYPE_USER_CHANGE_EMAIL)
                ->all();
            foreach($tokens as $token) $token->delete();

            $token = Token::create($user, Token::TOKEN_TYPE_USER_CHANGE_EMAIL, [
                'email' => $this->email,
                'tokenkey' => Token::createTokenKey()
            ]);

            return Yii::$app->mailer->compose('changeEmailToken', [
                'user' => $user,
                'tokenQueryParams' => $token->queryParams
            ])
            ->setFrom([env('ROBOT_EMAIL') => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('E-mail change for ' . Yii::$app->name)
            ->send();
        }

        return false;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email'=> 'New E-mail'
        ];
    }

    /**
     * Generate random activation code
     *
     * @return string
     */
    public static function generateEmailChangeKey(){
        return Yii::$app->security->generateRandomString(self::EMAIL_CHANGE_KEY_LENGTH);
    }

    /**
     * Get logged user
     *
     * @return User|null
     */
    protected function getUser()
    {
        //return logged user or null
        return (!Yii::$app->user->isGuest) ? Yii::$app->user->identity : null;
    }
}
