<?php

namespace app\controllers;

use app\models\Post;
use app\models\Tag;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Category;
use Yii;


final class TagController extends Controller
{
    public function actionView(string $slug): string
    {
        $tag = Tag::find()->where(['slug' => $slug])->one();
        if ($tag === null) {
            throw new NotFoundHttpException('Tag not found.');
        }

        $query = Post::find()
            ->innerJoin('{{%post_tag}} pt', 'pt.post_id = post.id')
            ->where(['pt.tag_id' => $tag->id, 'post.status' => Post::STATUS_PUBLISHED])
            ->orderBy(['post.published_at' => SORT_DESC, 'post.created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'pageSizeParam' => false,
            ],
        ]);

        return $this->render('view', [
            'tag' => $tag,
            'dataProvider' => $dataProvider,
        ]);
        $this->fillSidebar(null, $tag->slug);
    }
}
