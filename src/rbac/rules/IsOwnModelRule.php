<?php
namespace ant\rbac\rules;

use yii\rbac\Rule;

class IsOwnModelRule extends Rule
{
    const RULE_NAME = 'ant\rbac\rules\IsOwnModelRule';

	public $attribute = 'created_by';
    public $name = self::RULE_NAME;

    public function execute($user, $item, $params)
    {
		if (!isset($params['model'])) throw new \Exception('Param "model" is not set');
		
        $attribute = isset($params['attribute']) ? $params['attribute'] : $this->attribute;
        return $user && ($user === $params['model']->{$attribute} || $params['model']->isNewRecord);
    }
}