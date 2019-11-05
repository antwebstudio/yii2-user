<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

use ant\user\models\User;

/**
 * Change Password form
 */
class PasswordForm extends Model
{
    /**
     * @var
     */
    private $user;
    /**
     * @var
     */
    public $oldPassword;
    /**
     * @var
     */
    public $password;
    /**
     * @var
     */
    public $confirmPassword;
	
	public $needOldPassword = true;

    /**
     * Set user
     *
     * @return null
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['oldPassword', 'required'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['confirmPassword', 'required'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Password not match'],
        ];
    }

    /**
     * Change Password
     * 
     * @return boolean
     */
    public function changePassword()
    {
        if(!$this->needOldPassword || Yii::$app->user->identity->validatePassword($this->oldPassword)) {
            $user = $this->getUser();
            $user->setPassword($this->password);
            $user->generateAuthKey();
            return $this->user->save();
        }
		return false;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'oldPassword'=> 'Old Password',
            'password'=> 'Password',
            'confirmPassword'=> 'Confirm Password'
        ];
    }

    /**
     * Get User
     * 
     * @return User|null
     */
    protected function getUser()
    {
        return $this->user;
    }
}
