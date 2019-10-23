<?php
namespace ant\user\models;

use Yii;

use ant\user\models\UserInvite;
use ant\token\models\Token;
use ant\commands\SendEmailCommand;

class InviteRequest extends UserInvite
{
	public function init() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED');
	}
}
