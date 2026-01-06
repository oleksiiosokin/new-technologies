<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PostSearch represents the model behind the search form of `app\models\Post`.
 */
class PostSearch extends Post
{
    public $published_at_text;
    public $q;

    public function rules(): array
    {
        return [
            [['title', 'content', 'published_at_text', 'q'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null): ActiveDataProvider
    {
        $query = Post::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['or',
            ['like', 'title', $this->q],
            ['like', 'content', $this->q],
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['like', 'content', $this->content]);

        $input = trim((string)$this->published_at_text);
        if ($input !== '') {
            $start = null;
            $end = null;

            if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $input, $m)) {
                $day  = (int)$m[1];
                $mon  = (int)$m[2];
                $year = (int)$m[3];

                $start = mktime(0, 0, 0, $mon, $day, $year);
                $end   = mktime(23, 59, 59, $mon, $day, $year);
            }

            if ($start !== null && $end !== null) {
                $query->andWhere(['between', 'published_at', $start, $end]);
            }
        }


        return $dataProvider;
    }
}
