<?php

namespace ant\address\models;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\helpers\BaseJson;

use ant\behaviors\TimestampBehavior;
use ant\address\models\AddressCountry;
use ant\address\models\AddressZone;
use ant\address\models\query\AddressQuery;

/**
 * This is the model class for table "{{%address}}".
 *
 * @property integer $address_id
 * @property string $firstname
 * @property string $lastname
 * @property string $company
 * @property string $address_1
 * @property string $address_2
 * @property string $city
 * @property string $postcode
 * @property integer $country_id
 * @property integer $zone_id
 * @property smallinteger $readonly
 * @property smallinteger $del
 * @property integer $latitude
 * @property integer $longitude
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ant\address\models\AddressCountry $country
 * @property ant\address\models\AddressZone $zone
 */
class Address extends ActiveRecord
{
	const SCENARIO_DEFAULT = 'default';
    const SCENARIO_NO_REQUIRED = 'norequired';
    const SCENARIO_CUSTOM_STATE = 'customstate';
	const SCENARIO_VENUE = 'venue';
    const SCENARIO_MAILING_ADDRESS = 'mailing_address';
    const SCENARIO_COORDINATES = 'coordinates';
    const SCENARIO_VENUE_AND_COORDINATES = 'venue_and_coordinates';

    public $currentForm = false;

    private $_country_iso_2;

    private $_country_iso_3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
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
            TimestampBehavior::className(),
            [
				'class' => \ant\behaviors\DuplicatableBehavior::className(),
				'relations' => [],
			],
        ];
    }

    /**
     * @return addressQuery
     */
    public static function find()
    {
        return new AddressQuery(get_called_class());
    }
	
	// Fixed yii2 AttributeRecord getDirtyAttributes bug
	public function getDirtyAttributes($integerAttributes = []) {
		$dirtyAttributes = [];
		foreach(parent::getDirtyAttributes() as $attribute => $value) {
			$oldValue = $this->getOldAttribute($attribute);
			if (is_numeric($value) && is_numeric($oldValue)) {
				if ($value != $oldValue) {
					$dirtyAttributes[$attribute] = $value;
				}
			} else {
				$dirtyAttributes[$attribute] = $value;
			}
		}
		return $dirtyAttributes;
	}
	
	public function saveAsNewRecord($runValidation = true, $attributeNames = null) {
		if ($this->validate()) {
			return $this->duplicate();
		}
	}

    /**
     * @inheritdoc
     */
    public function saveAsNewRecordIfDirty($runValidation = true, $attributeNames = null)
    {
		if (count($this->getDirtyAttributes()) > 0) {
			return $this->saveAsNewRecord();
		}
		return $this;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->getCombinedRules([
            [['address_1', 'country_id', 'zone_id', 'postcode', 'city'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['address_1', 'country_id', 'custom_state', 'postcode', 'city'], 'required', 'on' => self::SCENARIO_CUSTOM_STATE],
            [[], 'required', 'on' => self::SCENARIO_NO_REQUIRED],
			[['venue'], 'required', 'on' => [self::SCENARIO_VENUE, self::SCENARIO_VENUE_AND_COORDINATES]],
            [['address_1', 'country_id', 'custom_state', 'postcode', 'city' , 'countryIso2','latitude' ,'longitude'], 'required', 'on' => self::SCENARIO_MAILING_ADDRESS],
            [['latitude' ,'longitude'], 'required' , 'on' => [self::SCENARIO_COORDINATES, self::SCENARIO_VENUE_AND_COORDINATES] ],
            //[['address_id', 'country_id', 'zone_id', 'latitude', 'longitude'], 'integer'],
            [['country_id', 'zone_id'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['firstname', 'lastname'], 'string', 'max' => 32],
            [['venue'], 'string'],
            [['address_1', 'address_2', 'postcode'], 'string', 'max' => 255],
            [['company'], 'string', 'max' => 64],
            [['custom_state', 'city'], 'string', 'max' => 128],
            [['postcode'], 'string', 'max' => 10],
            [['currentForm', 'countryIso2', 'countryIso3', 'venue', 'latitude' , 'longitude'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'firstname'     => 'First Name',
            'lastname'      => 'Last Name',
            'company'       => 'Company Name',
            'venue'         => 'Venue Name',
            'address_1'     => 'Street Name',
            'address_2'     => 'Address Line 2',
            'city'          => 'City',
            'postcode'      => 'Postcode',
            'country_id'    => 'Country',
            'countryIso2'   => 'Country',
            'countryIso3'   => 'Country',
            'zone_id'       => 'State',
            'custom_state'  => 'State',
            'readonly'      => 'Read Only',
            'del'           => 'Deleted',
            'latitude'      => 'Latitude',
            'longitude'     => 'Longitude',
            'created_at'    => 'Created At',
            'updated_at'    => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(AddressZone::className(), ['zone_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(AddressCountry::className(), ['id' => 'country_id']);
    }

    public function setCountryIso2($iso_code_2)
    {
        $country = AddressCountry::find()->where(['iso_code_2' => $iso_code_2])->one();
        if($country){
            $this->country_id = $country->id;
            $this->_country_iso_2 = $iso_code_2;
        }
    }

    public function getCountryIso2()
    {
        return $this->_country_iso_2;
    }

    public function setCountryIso3($iso_code_3)
    {
        $country = AddressCountry::find()->where(['iso_code_3' => $iso_code_3])->one();
        if($country){
            $this->country_id = $country->id;
            $this->_country_iso_3 = $iso_code_3;
        }
    }

    public function getCountryIso3()
    {
        return $this->_country_iso_3;
    }

    public function getAddressString() {
        if (trim($this->address_1) != '' && trim($this->address_2) != '') {
            return $this->address_1.', '.$this->address_2.'.';
        } else if (trim($this->address_2) != '') {
            return $this->address_2.'.';
        } else {
            return $this->address_1.'.';
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $address_string = '';

        $address_lines = [];
        if($this->address_1) $address_lines[] = $this->address_1;
        if($this->address_2) $address_lines[] = $this->address_2;
        if($address_lines) $address_string .= implode(', ', $address_lines) . '. ';

        if($this->postcode) $address_string .= $this->postcode . ' ';

        $address_zones = [];
        if($this->city) $address_zones[] = $this->city;
        if($this->zone_id) $address_zones[] = AddressZone::findOne($this->zone_id)->name;
        if($this->custom_state) $address_zones[] = $this->custom_state;
        if($address_zones) $address_string .= implode(', ', $address_zones) . '. ';

        if($this->country_id) $address_string .= AddressCountry::findOne($this->country_id)->name . '.';

        return rtrim($address_string);
    }
}
