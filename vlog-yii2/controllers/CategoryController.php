<?php

namespace app\controllers;

use app\models\Category;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

final class CategoryController extends Controller
{
    public function actionView(string $slug): string
    {
        $category = Category::find()->where(['slug' => $slug])->one();
        if ($category === null) {
            throw new NotFoundHttpException('Category not found.');
        }

        $query = Post::find()
            ->where([
                'category_id' => $category->id,
                'status' => Post::STATUS_PUBLISHED,
            ])
            ->orderBy(['published_at' => SORT_DESC, 'created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'pageSizeParam' => false,
            ],
        ]);

        return $this->render('view', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }
}
