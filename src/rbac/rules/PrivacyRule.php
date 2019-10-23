<?php
namespace ant\rbac\rules;

class PrivacyRule extends \yii\rbac\Rule {
	public $rules = [
		1 => true, // public
		2 => false, // private
	];
	public $allowOwner = true;
	public $attribute;
	
	public function init() {
		$this->name = self::className();
		parent::init();
	}
	
	public function execute($user, $item, $params) {
		return $this->checkPrivacy($user, $item, $params) || ($this->allowOwner && $this->checkOwnerAccess($user, $item, $params));
	}
	
	protected function checkOwnerAccess($user, $item, $params) {
		$rule = new \ant\rbac\rules\IsOwnModelRule;
		return $rule->execute($user, $item, $params);
	}
	
	protected function checkPrivacy($user, $item, $params) {
		$attribute = isset($params['attribute']) ? $params['attribute'] : ($this->attribute ? $this->attribute : 'privacy2');
		$model = $params['model'];
		return isset($this->rules[$model->{$attribute}]) ? $this->rules[$model->{$attribute}] : false;
	}
}