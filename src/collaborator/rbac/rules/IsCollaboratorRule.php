<?php
namespace ant\collaborator\rbac\rules;

use yii\rbac\Rule;

class IsCollaboratorRule extends \ant\rbac\rules\IsOwnModelRule
{
    const RULE_NAME = 'ant\collaborator\rbac\rules\IsCollaboratorRule';

	public $attribute = 'created_by';
    public $name = self::RULE_NAME;
    public $includeOwnedBy = true;

    public function execute($user, $item, $params)
    {
		if (!isset($params['model'])) throw new \Exception('Param "model" is not set');
        
        $model = $params['model'];

        return $user && ($model->haveCollaborator($user) || $params['model']->isNewRecord || ($this->includeOwnedBy && $params['model']->{$this->attribute} == $user));
    }
}