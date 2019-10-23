<?php
namespace ant\user\models;

use Yii;
use kartik\builder\Form;

/**
 * Create invite form
 */
class CreateUserInviteForm extends \ant\base\FormModel
{
	public $inviteType;
	
	public function models() {
		return [	
			'userInvite' => [
				'class' => 'ant\user\models\UserInvite',
				'as configurable' => [
					'class' => 'ant\behaviors\ConfigurableModelBehavior',
					'extraRules' => [
						['data', 'ant\validators\SerializableDataValidator', 'rules' => [
							['profile', 'ant\validators\SerializableDataValidator', 'rules' => [
								['firstname', 'required'],
								['lastname', 'required'],
								['contact', 'required'],
								['company', 'required'],
							]],
							['userConfig', 'ant\validators\SerializableDataValidator', 'rules' => [
								['currency', 'required'],
								['discount', 'required'],
							]],
						]],
					],
				],
			],
		];
	}
	
	public function getAvailableRoles() {
		return $this->userInvite->roles;
	}
	
	public function getCustomFormConfigs($form) {
		return [
			[
				'form' => $form,
				'model' => $this->getModel('userInvite'),
				'attributes' => [
					'data[profile][firstname]' => ['label' => 'Firstname', 'type' => Form::INPUT_TEXT],
					'data[profile][lastname]' => ['label' => 'Lastname', 'type' => Form::INPUT_TEXT],
					'data[profile][contact]' => ['label' => 'Contact', 'type' => Form::INPUT_TEXT],
					'data[profile][company]' => ['label' => 'Company', 'type' => Form::INPUT_TEXT],
					'data[userConfig][currency]' => [
						'label' => 'Currency', 
						'type' => Form::INPUT_DROPDOWN_LIST, 
						'options' => ['prompt' => ''],
						'items' => ['MYR' => 'MYR', 'USD' => 'USD', 'SGD' => 'SGD']
					],
					'data[userConfig][discount]' => [
						'label' => 'Discount Rate', 
						'type' => Form::INPUT_TEXT,
						'fieldConfig' => [
							'template' => '{label}<div class="input-group">{input}
							<span class="input-group-addon">%</span></div>{error}{hint}'
						],
					],
				],
			],
		];
	}
	
	public function sendInvite() {
		return $this->userInvite->sendInvite();
	}
}