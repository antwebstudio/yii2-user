<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

use ant\user\models\User;
use ant\token\models\Token;

/**
 * EmailChange form
 */
class EmailChangeForm extends Model
{
	public function init() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
	}
    /**
    * Change email by url token.
    *
    * @throws \yii\base\InvalidParamException if token is empty or not valid
    * @return ant\user\models\User
    */
    public function changeEmailByToken($tokenkey, $email)
    {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019
        if(
            empty($tokenkey) ||
            !is_string($tokenkey) ||
            empty($email) ||
            !is_string($email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) return false;

        $token = Token::find()
            ->byType(Token::TOKEN_TYPE_USER_CHANGE_EMAIL)
            ->byQueryParams([
                'tokenkey' => $tokenkey,
                'email' => $email
            ])
            ->isNotExpired()
            ->one();

        //if token found
        if(!$token) return false;

        //get token user
        $user = $token->user;

        //change user email
        $user->email = $email;

        if(!$user->save()) return false;

        $token->delete();

        return $user;
    }
}
