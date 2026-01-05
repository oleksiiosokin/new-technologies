<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category_id',
            'title',
            'slug',
            'content:ntext',
            [
                'attribute' => 'image_path',
                'format' => 'raw',
                'value' => $model->image_path
                    ? Html::img($model->image_path, ['style' => 'max-width:600px; max-height:350px; border-radius:8px;'])
                    : '—',
            ],
            'status',
            [
                'attribute' => 'published_at',
                'value' => $model->published_at ? date('d.m.Y H:i', (int)$model->published_at) : '—',
            ],
            [
                'attribute' => 'created_at',
                'value' => $model->created_at ? date('d.m.Y H:i', (int)$model->created_at) : '—',
            ],
            [
                'attribute' => 'updated_at',
                'value' => $model->updated_at ? date('d.m.Y H:i', (int)$model->updated_at) : '—',
            ],
            [
                'label' => 'Tags',
                'format' => 'raw',
                'value' => $model->tags
                    ? Html::encode(implode(', ', array_map(fn($t) => $t->name, $model->tags)))
                    : '—',
            ],
        ],
    ]) ?>

</div>
