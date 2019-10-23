<?php
namespace ant\user\models\search;

use yii\data\ActiveDataProvider;
use ant\user\models\User;

class UserSearch extends \ant\user\models\User {
    public $fullname;
	
	public function init() {
		throw new \Exception('Please use ant\user\models\UserSearch instead. ');
	}

    public function rules() {
        return [
            [['email', 'username', 'fullname'], 'safe'],
        ];
    }

    public function search($params) {
        $this->load($params);
        $query = User::find()->joinWith('profile profile');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'CONCAT(IFNULL(profile.firstname, "") ," ", IFNULL(profile.lastname, ""))', $this->fullname]);

        return $dataProvider;
    }
}