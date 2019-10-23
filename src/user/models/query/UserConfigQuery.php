<?php
namespace ant\user\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserConfigQuery
 * @package ant\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserConfigQuery extends ActiveQuery
{
    /**
     * @return $this
     */

    public function findByConfigName($configName, $userId) {
        return $this->andWhere(['config_name' => $configName, 'user_id' => $userId])->one() != null ? $this->andWhere(['config_name' => $configName, 'user_id' => $userId])->one() : null ;
    }

    public function findConfigs($userId) {
        return $this->andWhere(['user_id' => $userId])->all();
    }

}