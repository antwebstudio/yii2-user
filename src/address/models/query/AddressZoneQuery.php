<?php  
namespace ant\address\models\query;

use yii\db\ActiveQuery;

class AddressZoneQuery extends ActiveQuery
{
    public function search($param = array())
    {	
    	$this->andFilterWhere($param);
    	return $this;
    }

    /**
     * @return array
     */
    public function dropDownListForDepDrop()
    {
    	$dropDown = [];

        $data = $this->all();

        foreach ($data as $zone) {
            $dropDown[] = [
                'id' => $zone->id,
                'name' => $zone->name,
            ];
        }

        return $dropDown;
    }
}