<?php
namespace ant\user\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserProfileQuery
 * @package ant\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserProfileQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function isMain()
    {
        $this->andWhere(['main_profile' => 1]);
        return $this;
    }

    public function notMain()
    {
    	 $this->andWhere(['main_profile' => 0]);
        return $this;
    }
}