<?php
namespace ant\organization\models;

class Company extends Organization {
	
	public function attributeLabels() {
		return $this->getCombinedAttributeLabels(\yii\helpers\ArrayHelper::merge(parent::attributeLabels(), [
			'name' => 'Company Name',
		]));
	}
}