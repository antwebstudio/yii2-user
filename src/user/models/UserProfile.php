<?php
namespace ant\user\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;

use ant\behaviors\TimestampBehavior;
use ant\address\models\Address;
use ant\contact\models\Contact;
use ant\user\models\User;
use ant\user\models\query\UserProfileQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $token
 * @property integer $expire_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ant\user\models\User $user
 */
class UserProfile extends ActiveRecord
{
    const SCENARIO_SENSITIVE = 'sensitive';
	const SCENARIO_NO_REQUIRED = 'no_required';

    /**
     * No gender specify value
     */
    const GENDER_NONE = 0;
    /**
     * Male value
     */
    const GENDER_MALE = 1;
    /**
     * Female value
     */
    const GENDER_FEMALE = 2;

    /**
     * @var
     */
    public $picture;
    public $modelType = 'default';
    public $userUsing = 'admin';
    public $editingUser = 'company';

    public $adminUse = null;
	
	public static function ensureExist($userId) {
		$model = self::findOne(['user_id' => $userId]);
		if (!isset($model)) $model = self::createEmptyRecord($userId, true);
		
		return $model;
	}
	
	public static function createEmptyRecord($userId, $isMainProfile = false) {
		$model = new self(['scenario' => self::SCENARIO_NO_REQUIRED]);
		$model->user_id = $userId;
		if ($isMainProfile) $model->main_profile = 1;
		if (!$model->save()) throw new \Exception(Html::errorSummary($model));
		
		return $model;
	}

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url'
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
            [
                'class' => \ant\behaviors\SerializeBehavior::className(),
                'attributes' => ['data'],
                'serializeMethod' => \ant\behaviors\SerializeBehavior::METHOD_JSON,
            ],
            [
            	'class' => \ant\behaviors\AttachBehaviorBehavior::className(),
            	'config' => '@common/config/behaviors.php',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->getCombinedRules([
            [['firstname', 'lastname', 'contact_number', 'email'], 'required', 'on' => self::SCENARIO_SENSITIVE, 'except' => self::SCENARIO_NO_REQUIRED],
            [['user_id', 'gender', 'address_id', 'main_profile'], 'integer'],
            [['gender'], 'in', 'range' => [NULL, self::GENDER_NONE, self::GENDER_FEMALE, self::GENDER_MALE]],
            [['firstname', 'lastname', 'company', 'contact_number', 'avatar_path', 'avatar_base_url', 'company_website_url'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['title', 'nationality_id', 'picture', 'data', 'attachments'], 'safe'],
        ]);
        /*if (isset(Yii::$app->getModule('user')->profileFields[$this->modelType])
            && isset(Yii::$app->getModule('user')->profileFields[$this->modelType][$this->userUsing]) 
            && isset(Yii::$app->getModule('user')->profileFields[$this->modelType][$this->userUsing][$this->editingUser]) 
            && isset(Yii::$app->getModule('user')->profileFields[$this->modelType][$this->userUsing][$this->editingUser]['fields'])) {

        $fieldsArray = Yii::$app->getModule('user')->profileFields[$this->modelType][$this->userUsing][$this->editingUser]['fields'] == null ? Yii::$app->getModule('user')->getDefaultProfileFields() : Yii::$app->getModule('user')->profileFields;
        } else {
            $fieldsArray = Yii::$app->getModule('user')->getDefaultProfileFields();
        }
        if (!isset($fieldsArray[$this->modelType][$this->userUsing])) {
            $this->userUsing = Yii::$app->getModule('user')->profileDefaultUserUsing;
        }
        if (!isset($fieldsArray[$this->modelType][$this->userUsing][$this->editingUser])){
            $this->editingUser = Yii::$app->getModule('user')->profileDefaultEditingUser;
        }

        $arraysFieldsRule = [];
        $arrayFieldRules = [];
        foreach ($fieldsArray[$this->modelType][$this->userUsing][$this->editingUser]['fields'] as $key => $value) {
            foreach ($value as $fieldName => $fieldArray) {
                // example : $fieldName = profile['firstname']
                $stringOfArrayFieldName = explode("[", $fieldName);
                $fieldName = $stringOfArrayFieldName[0];
                if (count($stringOfArrayFieldName) != 1 ) {
                    if (isset($fieldArray['field']['rules'])) {
                        $arraysFieldsRule[$fieldName] = ArrayHelper::merge($fieldArray['field']['rules'], isset($arraysFieldsRule[$fieldName]) ? $arraysFieldsRule[$fieldName] : [] );
                    }
                } else {
                    // not data
                    if (isset($fieldArray['field']['rules'])) {
                        foreach ($fieldArray['field']['rules'] as $key => $rule) {
                            $rules[] = $rule;
                        }
                    }
                }
            }
            foreach ($arraysFieldsRule as $arrayFieldName => $arrayFieldsRule) {
                foreach ($arrayFieldsRule as $fieldRule) {
                    $arrayFieldRules[$arrayFieldName][] = $fieldRule;
                }
            }
            if ($arrayFieldRules != null) {
                foreach ($arrayFieldRules as $fieldName => $fieldRules) {
                    $rules[] = [$fieldName, \ant\validators\SerializableDataValidator::className(), 'rules' => $fieldRules,
                    ];
                }
            }
        }*/
        
        return $rules;
    }

    /**
     * @return UserProfileQuery
     */
    public static function find()
    {
        return new UserProfileQuery(get_called_class());
    }
	
	public static function findByUserId($userId) {
		return self::findOne(['user_id' => $userId]);
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->getCombinedAttributeLabels([
            'user_id' => 'User ID',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'company' => 'Company',
            'contact_number' => 'Contact Number',
            'avatar_path' => 'Avatar Path',
            'avatar_base_url' => 'Avatar Base Url',
            'gender' => 'Gender',
            'main_profile' => 'Main Profile',
            'address_id' => 'Address ID',
            'picture' => 'Picture',
            'company_website_url' => 'Company Website URL',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getContact() {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        $this->refresh();
        if(!$this->address_id){
            $address = new Address(['scenario' => Address::SCENARIO_NO_REQUIRED]);
            $address->save();
            $this->address_id = $address->id;
            $this->save();
        }
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }

    /**
     * @return null|string
     */
    public function getFullName()
    {
        if ($this->firstname || $this->lastname) {
            return implode(' ', [$this->firstname, $this->lastname]);
        }
        return null;
    }

    /**
     * @param null $default
     * @return bool|null|string
     */
    public function getAvatar($default = null)
    {
        return $this->avatar_path
            ? Yii::getAlias($this->avatar_base_url . '/' . $this->avatar_path)
            : $default;
    }

    public function getJson()
    {
        $attributes = $this->attributes;

        $attributes['email'] = $this->getEmail();

        return json_encode($attributes);
    }

    public function getEmail()
    {
        return $this->main_profile ? $this->getUser()->one()->email : $this->email;
    }

    public function setRole(){
        //set role accordingly
        $roles = $this->user->roles;
        $role = end($roles);
        $this->editingUser = $role->name;
        $user = User::findOne(Yii::$app->user->id);
        $currentRoles = $user->roles;
        $currentRole = end($currentRoles);
        $this->userUsing = $currentRole->name;
    }
}
