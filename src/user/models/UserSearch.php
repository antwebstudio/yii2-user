<?php

namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\user\models\User;

/**
 * UserSearch represents the model behind the search form about `ant\user\models\User`.
 */
class UserSearch extends User
{
    public $identityId;
    public $role;
    public $is_approved;
    public $approved_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_approved'], 'integer'],
            [['identityId', 'username', 'auth_key', 'role', 'password_hash', 'email', 'created_at', 'updated_at', 'logged_at', 'registered_ip', 'approved_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchByQuery($q) {
		$q = trim($q);
		
		$query = User::find()->alias('user');
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$query->joinWith('identityId identityId');
		$query->joinWith('profile profile');

        $query->orFilterWhere(['like', 'username', $q])
            //->orFilterWhere(['like', 'auth_key', $this->auth_key])
            //->orFilterWhere(['like', 'password_hash', $this->password_hash])
            ->orFilterWhere(['like', 'user.email', $q])
            ->orFilterWhere(['like', 'identityId.value', $q])
            ->orFilterWhere(['like', 'profile.contact_number', $q])
			->orFilterWhere(['like', 'profile.firstname', $q])
			->orFilterWhere(['like', 'profile.lastname', $q])
            //->orFilterWhere(['like', 'registered_ip', $q])
			;

        return $dataProvider;
    }

    protected function searchOr($params) {
        $query = User::find()->alias('user');
        
        // add conditions that should always apply here
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'username', $this->username])
            //->orFilterWhere(['like', 'auth_key', $this->auth_key])
            //->orFilterWhere(['like', 'password_hash', $this->password_hash])
            ->orFilterWhere(['like', 'user.email', $this->email])
            //->orFilterWhere(['like', 'registered_ip', $this->registered_ip])
			;

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {		
        $query = User::find()->alias('user');
        $query = $query->joinWith('profile profile');
		$query->joinWith('identityId identityId');
        
        // add conditions that should always apply here
        $this->load($params);

        if($this->role){
            $query->join('LEFT JOIN','{{%auth_assignment}}','{{%auth_assignment}}'.'.user_id = id')
            ->andFilterWhere(['{{%auth_assignment}}'.'.item_name' => $this->role]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.status' => $this->status,
            'user.created_at' => $this->created_at,
            'user.updated_at' => $this->updated_at,
            'user.logged_at' => $this->logged_at,
            'user.is_approved' => $this->is_approved,
            'user.approved_at' => $this->approved_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->orFilterWhere(['like', 'identityId.value', $this->identityId])
            //->andFilterWhere(['like', 'auth_key', $this->auth_key])
            //->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'user.email', $this->email])
            //->andFilterWhere(['like', 'registered_ip', $this->registered_ip])
			->andFilterWhere(['like', 'CONCAT(IFNULL(profile.firstname, "") ," ", IFNULL(profile.lastname, ""))', $this->fullname]);
			
        return $dataProvider;
    }
}