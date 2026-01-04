<?php

namespace app\controllers;

use app\models\Category;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Tag;
use Yii;


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
        $this->fillSidebar($category->slug, null);

    }

    private function fillSidebar(?string $activeCategorySlug = null, ?string $activeTagSlug = null): void
    {
        Yii::$app->view->params['categories'] = Category::find()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        Yii::$app->view->params['tags'] = Tag::find()
            ->orderBy(['name' => SORT_ASC])
            ->limit(30)
            ->all();

        Yii::$app->view->params['activeCategorySlug'] = $activeCategorySlug;
        Yii::$app->view->params['activeTagSlug'] = $activeTagSlug;
    }

}
