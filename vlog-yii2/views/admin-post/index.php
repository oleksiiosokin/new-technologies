<?php

use app\models\Post;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Img',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->image_path
                        ? \yii\helpers\Html::img($model->image_path, [
                            'style' => 'width:70px; height:auto; border-radius:6px;'
                        ])
                        : '';
                },
            ],

            'id',
            'title',
            'slug',

            [
                'attribute' => 'content',
                'value' => function($model) {
                    $text = strip_tags((string)$model->content);
                    return mb_strlen($text) > 120 ? mb_substr($text, 0, 120) . '…' : $text;
                }
            ],

            [
                'attribute' => 'published_at',
                'value' => function($model) {
                    return $model->published_at ? date('d.m.Y H:i', (int)$model->published_at) : '—';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],

    ]); ?>


</div>
