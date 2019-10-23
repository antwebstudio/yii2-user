<?php
namespace ant\organization\models\query;

class OrganizationQuery extends \yii\db\ActiveQuery {
	public function haveCollaborator($userId) {
		return $this->joinWith('collaborators collaborators')
			->andWhere(['collaborators.id' => $userId]);
	}
}