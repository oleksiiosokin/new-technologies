<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = $model->title;
?>
<article>
    <h1><?= Html::encode($model->title) ?></h1>

    <div style="opacity: .7; font-size: 14px; margin-bottom: 10px;">
        <?= Html::encode(date('Y-m-d H:i', (int)($model->published_at ?? $model->created_at))) ?>
        · Category:
            <?= Html::a(
                Html::encode($model->category?->name ?? '—'),
                ['category/view', 'slug' => $model->category?->slug]
            ) ?>
    </div>

    <?php if (!empty($model->image_path)): ?>
        <div style="margin: 12px 0;">
            <?= Html::img('/' . ltrim($model->image_path, '/'), [
                'alt' => $model->title,
                'style' => 'max-width: 640px; width:100%; height:auto; border-radius: 10px;',
            ]) ?>
        </div>
    <?php endif; ?>

    <div style="margin-top: 14px;">
        <?= nl2br(Html::encode($model->content)) ?>
    </div>

    <?php if ($model->tags): ?>
        <hr>
        <div>
            <strong>Tags:</strong>
            <?php foreach ($model->tags as $tag): ?>
                <?= Html::a(
                    Html::encode($tag->name),
                    ['tag/view', 'slug' => $tag->slug],
                    ['style' => 'display:inline-block; margin-right:6px; padding:2px 8px; border:1px solid #ccc; border-radius:999px; font-size:12px; text-decoration:none;']
                ) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <hr>
    <div style="opacity: .8;">
        Share:
        <?php
            $url = Yii::$app->urlManager->createAbsoluteUrl(['post/view', 'slug' => $model->slug]);
        ?>
        <?= Html::a('Copy link', $url, ['target' => '_blank', 'rel' => 'noopener']) ?>
    </div>
</article>
