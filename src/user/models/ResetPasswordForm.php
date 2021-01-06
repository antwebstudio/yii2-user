<?php
namespace ant\user\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;

use ant\user\models\User;
use ant\token\models\Token;

/**
 * Password reset form
 */
// TODO: [mlaxwong] Rename to PasswordResetForm
class ResetPasswordForm extends Model
{
    /**
     * @var
     */
    public $password;

    /**
     * @var
     */
    public $confirmPassword;

    private $_token;

    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($tokenkey, $email, $config = [])
    {
        if (empty($tokenkey) || !is_string($tokenkey) || empty($email) || !is_string($email)) {
            // TODO: [mlaxwong] change to http exceptino, make the code more clean
            throw new InvalidParamException('Password reset key cannot be blank.');
        }

        $this->_user = User::find()->andWhere(['email' => $email])->one();

        if(!$this->_user) throw new InvalidParamException('Wrong password reset detail.');

        $this->_token = Token::find()
            ->byUser($this->_user)
            ->byType(Token::TOKEN_TYPE_USER_PASSWORD_RESET)
            ->byQueryParams([
                'tokenkey' => $tokenkey,
                'email' => $email
            ])
            ->one();

        if (!$this->_token) throw new InvalidParamException('Wrong password reset detail.');

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['confirmPassword', 'required'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Password not match'],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $this->_user->password = $this->password;
        if(!$this->_user->save()) return false;
        $this->_token->delete();
        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password'=> \Yii::t('user', 'Password'),
            'confirmPassword'=> \Yii::t('user', 'Confirm Password'),
        ];
    }
}
