<?php
//namespace tests\codeception\common\collaborator\behaviors;
//use tests\codeception\common\UnitTester;
//use ant\event\models\Organizer;
use ant\user\models\User;
use ant\collaborator\models\CollaboratorGroup;


class CollaboratorBehaviorCest
{

    public $user = [];
    public $collaboratorGroup = [];
    public $model = [];
    //model is organizer

    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
        if ($this->user != null)
            $this->user->delete();
        if ($this->collaboratorGroup != null)
            $this->collaboratorGroup->delete();
        if ($this->model != null)
            $this->model->delete();
    }

    // tests
    public function testGetCollaborators(UnitTester $I)
    {
        $this->user = new User();
        $this->user->username = 'test';
        $this->user->email = 'test@email.com';
        $this->user->registered_ip=1;
        $this->user->status = 2;
        $this->user->password_hash = 'test';
        $this->user->auth_key = 'test';
        $this->collaboratorGroup = new CollaboratorGroup(['model_class_id' => \ant\models\ModelClass::getClassId(User::className())]);
        
        if($this->user->save() && $this->collaboratorGroup->save()) {
			$this->collaboratorGroup->link('users',$this->user);
			
            $model = $this->createOrganizer();
			$this->model = $model;
        } else {
			throw new \Exception('user or collaborator cannot save', 1);
		}

        $I->assertEquals(true,is_array($model->collaborators));

    }
    public function testHaveCollaborator(UnitTester $I)
    {
        $user = new User();
        $user->username = 'test';
        $user->email = 'test@email.com';
        $user->registered_ip=1;
        $user->status = 2;
        $user->password_hash = 'test';
        $user->auth_key = 'test';
        
        $collaboratorGroup = new CollaboratorGroup(['model_class_id' => \ant\models\ModelClass::getClassId(User::className())]);
        //throw new \yii\web\HttpException(404, $collaboratorGroup->getErrors());
        if($user->save() && $collaboratorGroup->save()) {
            //$user->link('collaborators',$collaboratorGroup);
            $model = $this->createOrganizer();
			$this->model = $model;
        } else {
			throw new \Exception('user or collaborator cannot save', 1);
		}
        
        foreach ($model->collaborators as $collaborator) {
			$userId=$collaborator->id;
			$I->assertEquals(true,$model->haveCollaborator($userId));
		}
    }

    protected function createOrganizer() {
        $data = [
            'name' => 'test organizer',
            'description' => 'test organizer description',
			'email' => 'organizer@email.com',
			'contact' => '0123456789',
        ];
        $organizer = new Organizer([
			'as collaborator' => [
				'class' => \ant\collaborator\behaviors\CollaboratorBehavior::class,
			],
		]);
        $organizer->attributes = $data;

        if (!$organizer->save()) throw new \Exception(print_r($organizer->errors, 1));

        return $organizer;
    }
	
	public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
                'dataFile' => '@tests/fixtures/data/user.php'
            ],
            'collaborator_group' => [
                'class' => \tests\fixtures\CollaboratorGroupFixture::className(),
                'dataFile' => '@tests/fixtures/data/collaborator_group.php'
            ],
        ];
    }
}

class Organizer extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%test}}';
	}
	
	public function rules() {
		return [
			[['name'], 'safe'],
		];
	}
	
}
