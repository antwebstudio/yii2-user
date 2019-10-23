<?php  
namespace ant\user\models\query;

use ant\user\models\User;
use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function notDeleted()
    {
        $this->andWhere(['!=', 'status', User::STATUS_DELETED]);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVATED]);
        return $this;
    }

    public function alive() {
        $this->andWhere(['in', 'status', User::STATUS_ALIVE]);
        return $this;
    }

    public function fromCountry($countryId) {
        if ($countryId) {
            return $this->joinWith(['profile' => function($q) {
                $q->joinWith('address address');
            }])->andWhere(['address.country_id' => $countryId]);
        }
        return $this;
    }

    public function registerDuring($startTime = null, $endTime = null) {
        return $this->alias('user')->andWhere(['>=', 'user.created_at', $startTime])
			->andWhere(['<=', 'user.created_at', $endTime]);
    }
}