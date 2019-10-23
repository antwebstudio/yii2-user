<?php 
namespace ant\collaborator\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

use ant\collaborator\models\CollaboratorGroup;
use ant\collaborator\models\CollaboratorGroupMap;
use ant\user\models\User;
use ant\event\models\Organizer;

class CollaboratorBehavior extends Behavior 
{
    public $className;
	public $collaboratorGroupIdAttribute    = 'collaborator_group_id';
	public $userIdAttribute                 = 'user_id'; // CollaboratorGroup table
	public $collaboratorGroupMapTable       = '{{%collaborator_group_map}}';
	public $idAttribute                     = 'id'; // User table

    protected $_collaborateGroup = null; 

    public function getCollaboratorGroup()
    {
       return $this->owner->hasOne(CollaboratorGroup::className(), ['id' => $this->collaboratorGroupIdAttribute]);
    }

    public function getCollaboratorGroupMap() {
        return $this->owner->hasMany(CollaboratorGroupMap::className(), ['collaborator_group_id' => 'id'])
            ->via('collaboratorGroup');
    }

	public function getCollaborators()
	{
        return $this->owner->hasMany(User::className(), ['id' => 'user_id'])
            ->via('collaboratorGroupMap');

		/*if(isset($this->owner->{$this->collaboratorGroupIdAttribute}))
     	{
			return $this->getCollaboratorGroup()->one()->hasMany(User::className(),[$this->idAttribute => $this->userIdAttribute])->viaTable($this->collaboratorGroupMapTable,[$this->collaboratorGroupIdAttribute => $this->idAttribute]);
        }*/
	}

    public function haveCollaborator($userId)
    {
		$relationQuery = $this->getCollaborators();
		if (isset($relationQuery)) {
			return $relationQuery->andWhere([$this->idAttribute => $userId])->one() == null ? false : true ;
		}
    }

    public function addCollaborator($userId)
    {
        $collaboratorGroup = $this->ensureCollaboratorGroup();
        if (isset($collaboratorGroup)) {
			if (!$this->haveCollaborator($userId)) {
				$user = User::findOne($userId);
				$collaboratorGroup->link('users', $user);
			}
        } else {
            throw new \Exception('Failed to add collaborator.');
		}
    }
	
	protected function ensureCollaboratorGroup() {
		if (isset($this->owner->collaboratorGroup)) {
			return $this->owner->collaboratorGroup;
		} else if (isset($this->owner->collaborator_group_id)) {
			return CollaboratorGroup::findOne($this->owner->collaborator_group_id);
		} else {
			$collaboratorGroup = new CollaboratorGroup();
			$collaboratorGroup->model_class_id = $this->getModelClassId();
			if (!$collaboratorGroup->save()) throw new \Exception('Failed to create collaborator group. ');
			
			$this->owner->collaborator_group_id = $collaboratorGroup->id;
			if (!$this->owner->save()) throw new \Exception('Failed to update collaborator_group_id. ');

			return $collaboratorGroup;
		}
	}

    protected function getModelClassId() {
        return \ant\models\ModelClass::getClassId($this->className);
    }
}
