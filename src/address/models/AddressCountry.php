<?php

namespace ant\address\models;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;

use ant\behaviors\TimestampBehavior;
use ant\address\models\AddressZone;
use ant\address\models\query\AddressCountryQuery;

/**
 * This is the model class for table "{{%address_country}}".
 *
 * @property integer $country_id
 * @property string $name
 * @property string $iso_code_2
 * @property string $iso_code_3
 *
 * @property ant\address\models\Zone[] $zones
 */
class AddressCountry extends ActiveRecord
{
    const VALUE_BY_ID = 'id';
    const VALUE_BY_ISO_2 = 'iso_code_2';
    const VALUE_BY_ISO_3 = 'iso_code_3';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address_country}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
				'class' => \ant\behaviors\DuplicatableBehavior::className(),	
			],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'iso_code_2', 'iso_code_3'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['iso_code_2'], 'string', 'length' => 2],
            [['iso_code_3'], 'string', 'length' => 3],
        ];
    }

    /**
     * @return addressCountryQuery
     */
    public static function find()
    {
        return new AddressCountryQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'Country ID',
            'name'          => 'Name',
            'iso_code_2'    => 'ISO Code 2',
            'iso_code_3'    => 'ISO Code 3',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZones()
    {
        return $this->hasMany(AddressZone::className(), ['country_id' => 'id']);
    }
}
