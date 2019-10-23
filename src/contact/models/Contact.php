<?php

namespace ant\contact\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use ant\address\models\Address;
use ant\user\models\User;
use ant\user\models\UserProfile;
use tuyakhov\notifications\NotifiableTrait;
use tuyakhov\notifications\NotifiableInterface;

/**
 * This is the model class for table "em_contact".
 *
 * @property integer $id
 * @property string $firstname
 * @property string $lastname
 * @property string $contact_name
 * @property string $organization
 * @property string $contact_number
 * @property string $email
 * @property integer $address_id
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Address $address
 */
class Contact extends \yii\db\ActiveRecord implements NotifiableInterface
{
    //use \ant\traits\SubobjectConfigTrait;
    use NotifiableTrait;
    
	const SCENARIO_ORGANIZATION_ONLY = 'organization_only';
	const SCENARIO_BILLING_REQUIRED_WITH_FIRSTNAME = 'billing_required_with_firstname';
    const SCENARIO_FULL_REQUIRED = 'full';
    const SCENARIO_FULL_EXCEPT_FIRSTNAME_REQUIRED = 'except_firstname';
    const SCENARIO_BASIC_REQUIRED = 'basic_required';
    const SCENARIO_NO_REQUIRED = 'no_required';

	public function behaviors() {
		return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
			['class' => \yii\behaviors\BlameableBehavior::className()],
            ['class' => \ant\behaviors\TimestampBehavior::className()],
            [
                'class' => \ant\behaviors\SerializeBehavior::className(),
                'attributes' => ['data'],
                'serializeMethod' => \ant\behaviors\SerializeBehavior::METHOD_JSON,
            ],
            [
				'class' => \ant\behaviors\DuplicatableBehavior::className(),
				'relations' => [],
			],
		];
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contact}}';
    }

    public $_addressString = null;

	public function fields() {
        return \yii\helpers\ArrayHelper::merge(parent::fields(), [
			'fullname',
		]);
	}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        
        return $this->getCombinedRules([
            [[], 'required', 'on' => self::SCENARIO_NO_REQUIRED],
			[['organization'], 'required', 'on' => self::SCENARIO_ORGANIZATION_ONLY],
            [['lastname', 'email', 'contact_number'], 'required', 'on' => self::SCENARIO_FULL_EXCEPT_FIRSTNAME_REQUIRED],
            [['firstname', 'lastname', 'email', 'contact_number'], 'required', 'on' => [self::SCENARIO_BILLING_REQUIRED_WITH_FIRSTNAME, self::SCENARIO_BASIC_REQUIRED]],
            [['firstname', 'lastname', 'email', 'contact_number','addressString', 'organization'], 'required', 'on' => self::SCENARIO_FULL_REQUIRED],
            [['address_id', 'status', 'created_by', 'updated_by'], 'integer'],
            //[['created_by', 'updated_by'], 'required'],
            [['firstname', 'lastname', 'email', 'contact_number', 'fax_number', 'addressString'], 'safe'],
            [['ic_passport', 'data', 'created_at', 'updated_at'], 'safe'],
            [['firstname', 'lastname', 'contact_name', 'organization', 'contact_number', 'email'], 'string', 'max' => 255],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['address_id' => 'id']],
			[['status'], 'default', 'value' => 0],
        ]);
    }

    public function duplicateWithAddress() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 2019-09-30
		$transaction = Yii::$app->db->beginTransaction();
        $newInvoice = $this->duplicate();
        
        if($this->address) {
            $newAddress = $this->address->duplicate();
            $newZone = $newAddress->zone->duplicate();
            $newCountry = $newAddress->country->duplicate();
            $newAddress->save();

            $newInvoice->address_id = $newAddress->id;
            $newInvoice->save();
        }

		$transaction->commit();
		return $newInvoice;
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->getCombinedAttributeLabels([
            'id' => 'ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'contact_name' => 'Contact Name',
            'organization' => 'Organization',
            'contact_number' => 'Contact Number',
            'email' => 'Email',
            'address_id' => 'Address ID',
            'status' => 'Status',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'addressString' => 'Address',
        ]);
    }
	
	public function getContactName() {
		if (isset($this->contact_name)) {
			return $this->contact_name;
		}
		return $this->firstname.' '.$this->lastname;
    }
    
    public function getFullAttributes() {
        $attributes = $this->attributes;
        $attributes['addressString'] = $this->addressString;
        return $attributes;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }

    public function getUserProfile() {
        return $this->hasOne(UserProfile::class, ['contact_id' => 'id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->via('userProfile');
    }

    public function setAddressString($value) {
		if (isset($this->address)) {
			$this->address->address_1 = $value;
			$this->_addressString = $value;
		} else {
			$this->_addressString = $value;
		}
    }

    public function getAddressString() {
		if (isset($this->address)) {
			return $this->address->address_1;
		} else {
			return $this->_addressString;
		}
    }
    
    public function getFullname() {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function setCustomState($value) {
        $this->ensureAddress(['custom_state' => $value]);
    }

    public function getCustomState() {
        $address = $this->ensureAddress();
        return $address->custom_state;
    }


    // i do not know how to test this just through a modified model (contact)
    // first the issue is caused by setAddressString using ensure address,
    // since this function will always return the address when address_id exist.
    // hence the profile address will be overwrite
    // contact should not save as profile address

    protected function ensureAddress($config = [], $validate = false) {

        if (!isset($this->address)) {
            if (isset($this->address_id)) {
                return Address::findOne($this->address_id);
            } else {
                $config['scenario'] = Address::SCENARIO_NO_REQUIRED;
                $address = new Address($config);
                if (!$address->save($validate)) throw new \Exception(Html::errorSummary($address));

                $this->link('address', $address);
                return $address;
            }
        } else {
            return $this->address;
        }
    }

    /*public function afterFind()
    {
        parent::afterFind();

        if($this->address_id) {
            $address = Address::find()->andWhere(['id' => $this->address_id])->one();
            if($address) {
                $this->addressString = $address->address_1;
            }
        }
    }*/

    protected function takeAddress() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 2019-10-06
		
        $address = $this->address ? $this->address : new Address();
        if(!$this->address) {
			//if (!YII_DEBUG) {// Added on 2019-09-30
				$address = Address::find()->andWhere(['id' => $this->address_id])->one();
				if(!$address) {
					$address = new Address();
				}
			//}
        }
        // guest no profile
        if (!Yii::$app->user->isGuest && isset(Yii::$app->user->identity->profile) && $address->id === Yii::$app->user->identity->profile->address->id) {
            $address = new Address ();
        }
        $address->scenario = Address::SCENARIO_NO_REQUIRED;

        return $address;
    }
	
	public function isUsedIn($class, $columnName) {
		$record = $class::findOne([$columnName => $this->id]);
		return isset($record);
	}
	
	public function saveAsNewRecord($runValidation = true, $attributeNames = null) {
		if ($this->validate()) {
			return $this->duplicate();
		}
	}
	
	public function saveAsNewRecordIfDirty($runValidation = true, $attributeNames = null) {
		
		$updatedAddress = false;
		$address = $this->address->saveAsNewRecordIfDirty();
		
		if (isset($address) && $this->address_id != $address->id) {
			$this->address_id = $address->id;
			$updatedAddress = true;
		}
		
		if (count($this->getDirtyAttributes()) > 0) {
			if (!$updatedAddress) {
				$address = $this->address->saveAsNewRecord();
				
				$old = $this->address_id;
				
				if (isset($address)) {
					$this->address_id = $address->id;
				}
			}
			return $this->saveAsNewRecord();
		} else {
			return $this->save();
		}
	}
	
	public function afterSave($insert, $changedAttributes) {
		$address = $this->ensureAddress();
		if (isset($this->_addressString)) {
			$address->address_1 = $this->_addressString;
			$address->save();
		}
		return parent::afterSave($insert, $changedAttributes);
	}

    /*public function save($runValidation = true, $attributeNames = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
		
		try {
			
			// $this->refresh();
			$address = $this->ensureAddress();
			if (isset($this->addressString)) $address->address_1 = $this->addressString;
			//if ($address->address_1 != 'Penang') throw new \Exception($address->address_1 . $address->scenario.($address->validate() ? 'y':'n'	));
			
			if (!$address->save())
			{
				$transaction->rollBack();
				return false;
			}
			
			$this->address_id = $address->id;
			if (!parent::save($runValidation, $attributeNames))
			{
				unset($this->address_id);
				$transaction->rollBack();
				return false;
			}

			$transaction->commit();
			return true;
		} catch (\Exception $ex) {
			$transaction->rollBack();
			throw $ex;
		}
		return false;
    }*/

}
