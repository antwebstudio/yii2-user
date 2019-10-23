<?php
namespace ant\rbac\rules;

class AuthenticatedUserRule extends \yii\rbac\Rule {
	public $activationNeeded = true;
	
	public function init() {
		$this->name = self::className();
		parent::init();
	}
	
	public function execute($user, $item, $params)
    {
		$user = \ant\user\models\User::findOne($user);
        return isset($user) && (!$this->activationNeeded || $user->isActive);
    }

}