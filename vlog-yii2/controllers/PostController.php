<?php

namespace app\controllers;

use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Category;
use app\models\Tag;
use app\models\Comment;
use yii\web\Response;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\data\Pagination;

final class PostController extends Controller
{

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-comment' => ['POST'],
                ],
            ],
        ];
    }


    public function actionIndex(): string
    {
        $this->fillSidebar(null, null);

        $query = Post::find()
            ->where(['status' => Post::STATUS_PUBLISHED])
            ->orderBy(['published_at' => SORT_DESC, 'created_at' => SORT_DESC]);

        $q = trim((string)Yii::$app->request->get('q', ''));

        if ($q !== '') {
            $query->andFilterWhere(['or',
                ['like', 'title', $q],
                ['like', 'content', $q],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'pageSizeParam' => false,
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
        $newComment = new Comment();
        $newComment->post_id = $model->id;

        $commentQuery = Comment::find()
            ->where([
                'post_id' => $model->id,
                'status' => 1,
                'parent_id' => null,
            ])
            ->orderBy(['created_at' => SORT_DESC]);

        $commentPagination = new Pagination([
            'totalCount' => (int)$commentQuery->count(),
            'pageSize' => 5,
            'pageParam' => 'cpage',
            'params' => array_merge(Yii::$app->request->get(), ['slug' => $slug]),
        ]);

        $comments = $commentQuery
            ->offset($commentPagination->offset)
            ->limit($commentPagination->limit)
            ->all();

        $rootIds = array_map(static fn($c) => (int)$c->id, $comments);

        $repliesByParent = [];
        if (!empty($rootIds)) {
            $replies = Comment::find()
                ->where(['status' => 1])
                ->andWhere(['parent_id' => $rootIds])
                ->orderBy(['created_at' => SORT_ASC])
                ->all();

            foreach ($replies as $r) {
                $repliesByParent[(int)$r->parent_id][] = $r;
            }
        }

        return $this->render('view', [
            'model' => $model,
            'newComment' => $newComment,
            'comments' => $comments,
            'repliesByParent' => $repliesByParent,
            'commentPagination' => $commentPagination,
        ]);

    }

    public function actionComment(string $slug): Response
    {
        $post = Post::find()
            ->where(['slug' => $slug, 'status' => Post::STATUS_PUBLISHED])
            ->one();

        if ($post === null) {
            throw new NotFoundHttpException('Post not found.');
        }

        $this->fillSidebar($post->category?->slug, null);

        $comment = new Comment();
        $comment->post_id = $post->id;

        if ($comment->load(Yii::$app->request->post())) {
            $comment->parent_id = $comment->parent_id ?: null;
            $comment->status = 1;

            $last = Comment::find()
                ->where(['author_name' => $comment->author_name, 'post_id' => $post->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            if ($last && (time() - (int)$last->created_at) < 30) {
                Yii::$app->session->setFlash('error', 'Занадто часто. Спробуй через 30 сек.');
                return $this->redirect(['post/view', 'slug' => $slug, '#' => 'comments']);
            }


            if ($comment->save()) {
                return $this->redirect(['post/view', 'slug' => $post->slug, '#' => 'comments']);
            }
        }

        $newComment = $comment;

        return $this->render('view', [
            'model' => $post,
            'newComment' => $newComment,
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

    public function actionDeleteComment(int $id, string $slug): Response
    {
        if (Yii::$app->user->isGuest || (int)Yii::$app->user->identity->is_admin !== 1) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $post = Post::find()->where(['slug' => $slug])->one();
        if ($post === null) {
            throw new NotFoundHttpException('Post not found.');
        }

        $comment = Comment::find()->where(['id' => $id, 'post_id' => $post->id])->one();
        if ($comment === null) {
            throw new NotFoundHttpException('Comment not found.');
        }

        Comment::deleteAll(['parent_id' => (int)$comment->id]); // видалити відповіді
        $comment->delete();

        Yii::$app->session->setFlash('success', 'Коментар видалено.');

        return $this->redirect(['post/view', 'slug' => $post->slug, '#' => 'comments']);
    }



}
