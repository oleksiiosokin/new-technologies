<?php

namespace app\controllers;

use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

final class PostController extends Controller
{
    public function actionIndex(): string
    {
        $query = Post::find()
            ->where(['status' => Post::STATUS_PUBLISHED])
            ->orderBy(['published_at' => SORT_DESC, 'created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'pageSizeParam' => false, // щоб юзер не крутив ?per-page=
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(string $slug): string
    {
        $model = Post::find()
            ->where(['slug' => $slug, 'status' => Post::STATUS_PUBLISHED])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Post not found.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
