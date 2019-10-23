<?php
namespace ant\user\notifications;

use Yii;
use tuyakhov\notifications\NotificationInterface;
use tuyakhov\notifications\NotificationTrait;

class Activation implements NotificationInterface {
    use NotificationTrait;
	
	public $user;
	public $activationUrl = ['user/activation/token-activation'];

    public function __construct($user) {
        $this->user = $user;
    }
	
    public function exportForMail() {
		$view = '@ant/user/mails/accountActivation';
		$view = file_exists(Yii::getAlias($view).'.php') ? $view : 'accountActivation';
		
		return \Yii::createObject([
			'class' => '\tuyakhov\notifications\messages\MailMessage',
			'subject' => 'Account activation for ' . Yii::$app->name,
			'view' => $view,
			'viewData' => $this->getMailParams(),
		]);
    }
	
	protected function getMailParams() {
		$token = $this->user->generateActivationToken();
		
		return [
			'user' => $this->user,
			'activationLink' => \Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(\yii\helpers\ArrayHelper::merge($this->activationUrl, $token->queryParams)),
			'activationCode' => $token->queryParams['code'],
		];
	}
}