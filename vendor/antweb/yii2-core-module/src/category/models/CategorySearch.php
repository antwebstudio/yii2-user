<?php

namespace ant\category\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ant\category\models\Category;

/**
 * CategorySearch represents the model behind the search form about `ant\article\models\Category`.
 */
class CategorySearch extends Category
{
	public $parent;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attachments', 'attachments2', 'thumbnail', 'banner', 'parent'], 'safe'],
            [['id', 'parent_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['slug', 'title', 'body', 'subtitle', 'type_id'], 'safe'],
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
        $query = Category::find()->joinWith('type type');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => ['defaultOrder' => ['left' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
		if (isset($this->parent_id)) {
            $query->childrenOf($this->parent_id, 1);
		}
		
		if (is_null($this->type_id)) {
			$query->andWhere(['type_id' => null]);
		} else {
			$query->typeOf($this->type_id);
		}
		if (isset($this->parent)) {
			$query->childrenOf($this->parent, 1);
		}
		
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'body', $this->body])
            ->andFilterWhere(['like', 'subtitle', $this->subtitle]);
            
        return $dataProvider;
    }
}
