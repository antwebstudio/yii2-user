<?php

namespace ant\address\models;

use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;

use ant\behaviors\TimestampBehavior;
use ant\address\models\AddressCountry;
use ant\address\models\query\AddressZoneQuery;

/**
 * This is the model class for table "{{%address_zone}}".
 *
 * @property integer $zone_id
 * @property integer $country_id
 * @property string $name
 * @property string $code
 *
 * @property ant\address\models\AddressCountry $country
 */
class AddressZone extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address_zone}}';
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
     * @return addressQuery
     */
    public static function find()
    {
        return new AddressZoneQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'name', 'code'], 'required'],
            [['country_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'country_id'    => 'Country ID',
            'name'          => 'Name',
            'code'          => 'Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(AddressCountry::className(), ['country_id' => 'id']);
    }
}
