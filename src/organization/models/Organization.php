<?php

namespace ant\organization\models;

use Yii;

use ant\contact\models\Contact;
use ant\user\models\User;

/**
 * This is the model class for table "organization".
 *
 * @property int $id
 * @property string $name
 * @property int $contact_id
 * @property int $status
 * @property int $founded_year
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Contact $contact
 * @property OrganizationUserMap[] $organizationUserMaps
 */
class Organization extends \yii\db\ActiveRecord
{
	const SCENARIO_ALL_REQUIRED = 'all_required';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%organization}}';
    }
	
	public function behaviors() {
		return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
            'collaborator' => 
            [
                'class'=> \ant\collaborator\behaviors\CollaboratorBehavior::className(),
            ],
			[
				'class' => \voskobovich\linker\LinkerBehavior::className(),
				'relations' => [
                    'user_ids' => 'users',
				],
			],
            [
                'class' => \ant\behaviors\SerializableAttribute::class,
                'attributes' => ['data'],
            ],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return $this->getCombinedRules([
            [['name'], 'required'],
			[['founded_year'], 'required', 'on' => self::SCENARIO_ALL_REQUIRED],
            [['contact_id', 'status', 'founded_year'], 'integer'],
            [['website_url', 'registration_number', 'data', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
        ]);
    }
	
	public static function find() {
		return new \ant\organization\models\query\OrganizationQuery(get_called_class());
	}
	
	public static function createForUser($user, $name = 'Default') {
		$userId = is_object($user) ? $user->id : $user;
		
		$contact = new Contact;
		if (!$contact->save()) throw new \Exception(print_r($contact->errors, 1));
		
		$organization = new Organization;
		$organization->name = $name;
		$organization->contact_id = $contact->id;
		if (!$organization->save()) throw new \Exception(print_r($organization->errors, 1));
		
		$organization->addCollaborator($userId);
		
		return $organization;
	}
	
	public static function ensureForUser($user, $name = 'Default') {
		$userId = is_object($user) ? $user->id : $user;
		$organization = Organization::find()->haveCollaborator($userId)->one();

		if (!isset($organization)) {
			$organization = self::createForUser($user, $name);
		}
		return $organization;
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Organization Name',
            'contact_id' => 'Contact ID',
            'status' => 'Status',
            'founded_year' => 'Founded Year',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getUsers() {
		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('{{%organization_user_map}}', ['organization_id' => 'id']);
	}
	
    public function getUserMaps()
    {
        return $this->hasMany(OrganizationUserMap::className(), ['organization_id' => 'id']);
    }
}
