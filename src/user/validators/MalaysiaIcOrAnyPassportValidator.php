<?php
namespace ant\user\validators;

class MalaysiaIcOrAnyPassportValidator extends \yii\validators\Validator {
	
	public $addError = null;

	public function init() {
        parent::init();
		
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
	}
	
	/*public function validateValue($value) {
		if (strlen($value) != 12 && $this->addError == null) {
			return ['Format Malaysia IC wrong.',[]];
		} else {
			$numbers = preg_match('/[^0-9]/', $value);
			if ($numbers == 0) {
				return null;
			} else {
				return ['Format Malaysia IC wrong.',[]];
			}
		}
	}*/

	public function validateAttribute($model, $attribute) {
        $value = $model->{$attribute};
        $value = str_replace('-', '', $value);
		
		if (preg_match('/[^0-9a-zA-Z]/', $value)) {
			$this->addError($model, $attribute, $this->message);
		} else if (strlen($value) > 14) {
			$this->addError($model, $attribute, $this->message);
		}
    }
}