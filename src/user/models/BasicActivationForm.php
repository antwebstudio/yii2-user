<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

use ant\user\models\User;
use ant\user\models\ActivationCodeRequestForm;
use ant\token\models\Token;

/**
 * Activation form
 */
class BasicActivationForm extends Model
{
    /**
     * @var
     */
    public $activationCode;
	public $email;
	
    protected $_user;
    protected $_token;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['activationCode', 'trim'],
            [['activationCode', '!email'], 'required'],
            ['activationCode', 'string', 'length' => ActivationCodeRequestForm::ACTIVATION_CODE_LENGTH],
        ];
    }
	
	public function checkActivation() {
		return $this->user->isActive;
	}
	
	public function activate() {
		if ($this->validate()) {
			return $this->activateUser($this->getTokenData());
		}
		return false;
	}

    /**
    * Ativate user by activation code.
    *
    * @return User|boolean|null
    */
    /*public function activateByCode()
    {

        return $this->activateUser([
            'activation_code' => $this->activationCode,
            'email' => $this->email,
        ]);
    }*/

    /**
    * Ativate user by url.
    *
    * @return User|boolean|null
    */
    /*public function activateByUrl($email, $activationCode)
    {
        if (
            empty($email)           || !is_string($email)           ||
            empty($activationCode) || !is_string($activationCode)
        ) return false;

        return $this->activateUser([
            'email' => $email,
            'activation_code' => $activationCode,
        ]);
    }*/

    /**
     * Get logged user
     *
     * @return User|null
     */
    public function getUser()
    {
        if (!isset($this->_user)) {
            $this->_user = User::find()->andWhere(['email' => $this->email])->one();
        }
        return $this->_user;
    }

    public function getIsTokenValid() {
        return isset($this->token);
    }
	
	public function getExpectedCodeLength() {
		return ActivationCodeRequestForm::ACTIVATION_CODE_LENGTH;
	}

    protected function getToken() {
        if (!isset($this->_token)) {
            $this->_token = Token::find()
                ->byUser($this->user)
                ->byType(Token::TOKEN_TYPE_USER_ACTIVATION)
                ->byQueryParams($this->getTokenData())
                ->one();
        }
        return $this->_token;
    }

    protected function getTokenData() {
        return [
            'code' => $this->activationCode,
            'email' => $this->email,
        ];
    }

    /**
     * activate user
     * @param  array $tokenData
     * @param  User|null $loggedUser
     * @return User|boolen
     */
    protected function activateUser()
    {
        if(!isset($this->user)) {
			$this->addError('email', 'User email not matched.');
			return false;
		}

        if(!isset($this->token)) {
			$this->addError('activationCode', 'Wrong validate code.');
			return false;
		}

        //activate user
        if(!$this->user->activate()) {
			$this->addError('email', 'User record cannot be saved.');
			return false;
		}

        //delete token
        $this->token->delete();

        //return token user
        return $this->user;
    }
}
