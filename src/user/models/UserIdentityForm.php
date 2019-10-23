<?php
namespace ant\user\models;

use Yii;
use yii\helpers\Html;
use ant\user\models\User;
use ant\user\models\UserIdentity;

class UserIdentityForm extends \ant\base\FormModel
{
	public $userId;
	public $identitiesValue;
	public $types = [
		'ic' => 'NRIC Number',
	];
	
	public function init() {
		parent::init();
		
		$this->identitiesValue = UserIdentity::find()->andWhere(['user_id' => $this->userId])->asArray()->all();
	}
	
	public function rules() {
		return [
			[['identitiesValue'], 'safe']
		];
	}
	
	public function save($runValidation = true) {
		foreach ($this->identitiesValue as $identity) {
			if ($identity['id']) {
				$model = UserIdentity::findOne($identity['id']);
			} else {
				$model = new UserIdentity(['user_id' => $this->userId]);
			}
			$model->load($identity, '');
			
			if (!$model->save()) {
				throw new \Exception('Failed to create user identity record. '.Html::errorSummary($model));
			}
		}
		return true;
	}
}