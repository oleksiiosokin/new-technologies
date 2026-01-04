<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Post $model */

$this->title = $model->title;

$publishedTs = (int)($model->published_at ?? $model->created_at);
$publishedLabel = date('Y-m-d H:i', $publishedTs);

$absoluteUrl = Yii::$app->urlManager->createAbsoluteUrl(['post/view', 'slug' => $model->slug]);

$shareTelegram = 'https://t.me/share/url?' . http_build_query(['url' => $absoluteUrl, 'text' => $model->title]);
$shareTwitter  = 'https://twitter.com/intent/tweet?' . http_build_query(['url' => $absoluteUrl, 'text' => $model->title]);
$shareFacebook = 'https://www.facebook.com/sharer/sharer.php?' . http_build_query(['u' => $absoluteUrl]);
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0"><?= Html::encode($model->title) ?></h1>
        <div class="post-meta mt-1">
            <?= Html::encode($publishedLabel) ?>
            ·
            <?= Html::a(
                Html::encode($model->category?->name ?? '—'),
                $model->category ? ['category/view', 'slug' => $model->category->slug] : ['post/index'],
                ['class' => 'text-decoration-none']
            ) ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <?= Html::a('← Усі пости', ['post/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        <?= Html::a('Copy link', $absoluteUrl, ['class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank', 'rel' => 'noopener']) ?>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <?php if (!empty($model->image_path)): ?>
            <div class="mb-3">
                <?= Html::img('/' . ltrim($model->image_path, '/'), [
                    'alt' => $model->title,
                    'class' => 'img-fluid post-cover',
                    'style' => 'width:100%; max-height: 420px; object-fit: cover;',
                ]) ?>
            </div>
        <?php endif; ?>

        <div class="fs-6" style="line-height: 1.7;">
            <?= nl2br(Html::encode($model->content)) ?>
        </div>

        <?php if ($model->tags): ?>
            <hr class="my-4">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="text-muted">Tags:</div>
                <div>
                    <?php foreach ($model->tags as $tag): ?>
                        <?= Html::a(
                            Html::encode($tag->name),
                            ['tag/view', 'slug' => $tag->slug],
                            ['class' => 'badge text-bg-secondary me-1 mb-1 text-decoration-none']
                        ) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-footer bg-transparent border-0 pt-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="text-muted">Share:</div>
            <div class="d-flex flex-wrap gap-2">
                <?= Html::a('Telegram', $shareTelegram, ['class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank', 'rel' => 'noopener']) ?>
                <?= Html::a('Twitter/X', $shareTwitter, ['class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank', 'rel' => 'noopener']) ?>
                <?= Html::a('Facebook', $shareFacebook, ['class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank', 'rel' => 'noopener']) ?>
            </div>
        </div>
    </div>
</div>

