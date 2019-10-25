<?php

namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\user\models\UserInvite;

/**
 * UserInviteSearch represents the model behind the search form about `ant\user\models\UserInvite`.
 */
class UserInviteSearch extends UserInvite
{
    /**
     * @inheritdoc
     */
    public $userEmail;
    public function rules()
    {
        return [
            [['id', 'status', 'created_by', 'updated_by', 'token_id'], 'integer'],
            [['email', 'role', 'created_at', 'updated_at', 'data', 'userEmail', 'type'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserInvite::find()->joinWith('user user')->andWhere(['or', ['type' => null], ['type' => \ant\user\backend\Module::INVITE_TYPE_ROLE] ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['userEmail'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
        'asc' => ['user.email' => SORT_ASC],
        'desc' => ['user.email' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'token_id' => $this->token_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'role', $this->role])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
