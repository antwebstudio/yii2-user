<?php
namespace ant\user\models;

use \yii\db\ActiveRecord;

class UpdateEmailForm extends \ant\base\FormModel {
	public $email;
	
	public function init() {
		if (!isset($this->email) && isset($this->user)) {
			$this->email = $this->user->email;
		}
	}
	
	public function models() {
		return [
			'user' => [	
				'class' => 'ant\user\models\User',
				'on '.ActiveRecord::EVENT_BEFORE_UPDATE => function($event) {
					$user = $event->sender;
					$user->email = $this->email;
				}
			],
		];
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
            ['email', 'unique', 'when' => function($model) {
				return $this->email != $this->user->email;
			}, 'targetClass' => '\ant\user\models\User', 'message' => 'This email has already been taken.'],
        ];
    }
}