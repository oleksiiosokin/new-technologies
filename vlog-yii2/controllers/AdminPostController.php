<?php

namespace app\controllers;

use app\models\Post;
use app\models\PostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AdminPostController implements the CRUD actions for Post model.
 */
class AdminPostController extends Controller
{

    
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }


    /**
     * Lists all Post models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Post();
        $model->loadDefaultValues();

        if ($this->request->isPost && $model->load($this->request->post())) {

            $now = time();
            $model->created_at = $now;
            $model->updated_at = $now;

            $rawPublishedAt = $this->request->post('Post')['published_at'] ?? null;
            if ($rawPublishedAt !== null && $rawPublishedAt !== '') {
                $ts = strtotime($rawPublishedAt);
                $model->published_at = ($ts !== false) ? $ts : null;
            } else {
                $model->published_at = null;
            }

            if ((int)$model->status !== Post::STATUS_PUBLISHED) {
                $model->published_at = null;
            } elseif ($model->published_at === null) {
                $model->published_at = $now;
            }

            if (!$model->uploadImage()) {
                return $this->render('create', ['model' => $model]);
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            $now = time();
            $model->updated_at = $now;

            $rawPublishedAt = $this->request->post('Post')['published_at'] ?? null;
            if ($rawPublishedAt !== null && $rawPublishedAt !== '') {
                $ts = strtotime($rawPublishedAt);
                $model->published_at = ($ts !== false) ? $ts : null;
            } else {
                $model->published_at = null;
            }

            if ((int)$model->status !== Post::STATUS_PUBLISHED) {
                $model->published_at = null;
            } elseif ($model->published_at === null) {
                $model->published_at = $now;
            }

            if (!$model->uploadImage()) {
                return $this->render('update', ['model' => $model]);
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }



    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
}
