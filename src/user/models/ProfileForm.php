<?php
namespace ant\user\models;

use Yii;
use ant\user\models\User;
use ant\user\models\UserProfile;
use ant\address\models\Address;
/*use yii\base\Model;
use yii\base\InvalidParamException;

use ant\token\models\Token;*/

/**
 * EmailChange form
 */
class ProfileForm extends \ant\base\FormModel
{
	public $userId;
	
    /**
    * Change email by url token.
    *
    * @throws \yii\base\InvalidParamException if token is empty or not valid
    * @return ant\user\models\User
    */
    public function configs()
    {
		return [
			'profile' => [
				'class' => UserProfile::class,
				'on '.\yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE => function($event) {
					$userProfile = $event->sender;
					
					if ($userProfile->isNewRecord) {
						$userProfile->user_id = $this->userId;
					}
				},
			],
			'address:optional' => [
				'class' => Address::class,
				'scenario' => Address::SCENARIO_NO_REQUIRED,
			],
		];
    }
	
	public function getFormAttributes($name = null) {
		return [
			'picture' => [
				'next' => true,
				'type' => 'widget',
				'widgetClass' => \trntv\filekit\widget\Upload::className(),
				'options' => [
					'url' => [
						'avatar-upload'
					]
				],
			],
			'firstname' => [
				'next' => true,
				'type' => 'textInput',
			],
			'lastname' => [
				'next' => true,
				'type' => 'textInput',
			],                            
			'company' => [
				'next' => true,
				'type' => 'textInput',
			],
			'gender' => [
				'next' => true,
				'type' => 'dropdownList',
				'items' => [
					0 => 'Select ...',
					UserProfile::GENDER_MALE => 'Male',
					UserProfile::GENDER_FEMALE => 'Female',
				],
			],
			'contact' => [
				'next' => true,
				'type' => 'textInput',
			],
		];
		
		//throw new \Exception(print_r(array_keys($this->getModel($name)->attributes),1));
		$attributes = [];
		
		foreach ($this->getModel($name)->attributes as $attribute => $value) {
			$attributes[$attribute] = [
				'attribute' => $attribute,
			];
		}
		
		return $attributes;
	}

	
	public function getGridFormRows($name = null) {
		//throw new \Exception(print_r(array_keys($this->getModel($name)->attributes),1));
		$attributes = [];
		
		foreach ($this->getModel($name)->attributes as $attribute => $value) {
			$attributes[$attribute] = [
				'attribute' => $attribute,
			];
		}
		
		return [
			[
				'attributes' => $this->getFormAttributes($name),
			],
		];
	}
}
