<?php

namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\user\models\UserProfile;

/**
 * UserProfileSearch represents the model behind the search form about `ant\user\models\UserProfile`.
 */
class UserProfileSearch extends UserProfile
{
    public $fullName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['fullName', 'email', 'created_at', 'updated_at'], 'safe'],
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
        $query = UserProfile::find()->notMain();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                    'fullName' => [
                        'asc' => ['firstname' => SORT_ASC, 'lastname' => SORT_ASC],
                        'desc' => ['firstname' => SORT_DESC, 'lastname' => SORT_DESC],
                        'label' => 'Full Name',
                        'default' => SORT_ASC
                    ],
                ]
            ),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'CONCAT(firstname, " ", lastname)', $this->fullName]);

       /* $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);*/

        return $dataProvider;
    }
}
