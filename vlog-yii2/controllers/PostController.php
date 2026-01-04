<?php

namespace app\controllers;

use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Category;
use app\models\Tag;
use Yii;

final class PostController extends Controller
{
    public function actionIndex(): string
    {
        $this->fillSidebar(null, null);

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
        $this->fillSidebar($model->category?->slug, null);
        return $this->render('view', [
            'model' => $model,
        ]);

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
