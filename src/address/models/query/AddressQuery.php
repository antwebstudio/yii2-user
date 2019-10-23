<?php  
namespace ant\address\models\query;

use ant\user\models\User;
use yii\db\ActiveQuery;

class AddressQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function readonly()
    {
        $this->andWhere(['readonly', 1]);
        return $this;
    }

    public function search($param = array())
    {
    	print_r($param);die;
    }
}