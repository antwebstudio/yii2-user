<?php
namespace ant\user\notifications;

use Yii;
use tuyakhov\notifications\NotificationInterface;
use tuyakhov\notifications\NotificationTrait;

class SignupWelcome implements NotificationInterface {
    use NotificationTrait;
	
	public $user;

    public function __construct($user) {
        $this->user = $user;
    }
	
    public function exportForMail() {
		$view = '@ant/user/mails/signup-welcome';
		//$view = file_exists(Yii::getAlias($view).'.php') ? $view : 'accountActivation';
		
		return \Yii::createObject([
			'class' => '\tuyakhov\notifications\messages\MailMessage',
			'subject' => 'Welcome to ' . Yii::$app->name,
			'view' => $view,
			'viewData' => [
				'user' => $this->user,
			],
		]);
    }
}