<?php
namespace ant\user\models;

use yii\helpers\ArrayHelper;
use ant\user\models\ActivationCodeRequestForm;

class ActivationForm extends BasicActivationForm {
    public $password;
    public $confirmPassword;

    public function rules() {
        return ArrayHelper::merge(parent::rules(), [
            [['password', 'confirmPassword'], 'safe'],
            [['password'], 'required'],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'password'],
        ]);
    }

    public function activate() {
        if ($this->validate()) {
            $this->updatePassword();
        }
        return $this->activateUser($this->getTokenData());
    }
	
	public function getExpectedCodeLength() {
		return ActivationCodeRequestForm::ACTIVATION_CODE_LENGTH;
	}

    protected function updatePassword() {
        $this->user->setPassword($this->password);
        $this->user->save();
    }
}