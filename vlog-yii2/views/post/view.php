<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Post $model */
/** @var app\models\Comment $newComment */


$this->title = $model->title;

$publishedTs = (int)($model->published_at ?: $model->created_at);
$updatedTs   = (int)($model->updated_at ?: 0);
$createdTs   = (int)($model->created_at ?: 0);

$publishedLabel = date('d-m-Y H:i', $publishedTs);

$showUpdated = $updatedTs && $createdTs && $updatedTs > $createdTs;
$updatedLabel = $showUpdated ? date('d-m-Y H:i', $updatedTs) : null;

$absoluteUrl = Yii::$app->urlManager->createAbsoluteUrl(['post/view', 'slug' => $model->slug]);

$shareTelegram = 'https://t.me/share/url?' . http_build_query(['url' => $absoluteUrl, 'text' => $model->title]);
$shareTwitter  = 'https://twitter.com/intent/tweet?' . http_build_query(['url' => $absoluteUrl, 'text' => $model->title]);
$shareFacebook = 'https://www.facebook.com/sharer/sharer.php?' . http_build_query(['u' => $absoluteUrl]);
$rootComments = $model->getComments()->with('replies')->all();
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0"><?= Html::encode($model->title) ?></h1>
        <div class="post-meta mt-1">
            <span><strong>Опубліковано:</strong> <?= Html::encode($publishedLabel) ?></span>

            <?php if ($showUpdated): ?>
                <span class="ms-3"><strong>Оновлено:</strong> <?= Html::encode($updatedLabel) ?></span>
            <?php endif; ?>

            <span class="mx-2">·</span>

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
            <div class="post-cover-wrap mb-3">
                <?= Html::img('/' . ltrim($model->image_path, '/'), [
                    'alt' => $model->title,
                    'class' => 'img-fluid post-cover',
                    'style' => 'width:100%',
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
<div id="comments" class="card">
    <div class="card-header fw-semibold">Коментарі</div>
    <div class="card-body">
        <?php if (empty($rootComments)): ?>
            <div class="text-muted">Поки що немає коментарів.</div>
        <?php else: ?>
            <?php foreach ($rootComments as $c): ?>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <strong><?= Html::encode($c->author_name) ?></strong>
                            <span class="text-muted ms-2"><?= Html::encode(date('d-m-Y H:i', (int)$c->created_at)) ?></span>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary"
                                    type="button"
                                    onclick="document.getElementById('comment-parent-id').value='<?= (int)$c->id ?>'; document.getElementById('comment-form-title').innerText='Відповідь користувачу <?= Html::encode($c->author_name) ?>'; document.getElementById('comment-author-name').focus();">
                                Reply
                            </button>

                            <?php if (!Yii::$app->user->isGuest && (int)Yii::$app->user->identity->is_admin === 1): ?>
                                <?= Html::a('Delete', ['post/delete-comment', 'id' => (int)$c->id, 'slug' => $model->slug], [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Видалити цей коментар (і відповіді до нього)?',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>


                    <div class="mt-2" style="white-space: pre-wrap;"><?= Html::encode($c->content) ?></div>

                    <?php if ($c->replies): ?>
                        <div class="mt-3 ms-3 ps-3 border-start">
                            <?php foreach ($c->replies as $r): ?>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= Html::encode($r->author_name) ?></strong>
                                            <span class="text-muted ms-2"><?= Html::encode(date('d-m-Y H:i', (int)$r->created_at)) ?></span>
                                        </div>

                                        <?php if (!Yii::$app->user->isGuest && (int)Yii::$app->user->identity->is_admin === 1): ?>
                                            <?= Html::a('Delete', ['post/delete-comment', 'id' => (int)$r->id, 'slug' => $model->slug], [
                                                'class' => 'btn btn-sm btn-outline-danger', // Класи як у головної кнопки
                                                'data' => [
                                                    'method' => 'post',
                                                    'confirm' => 'Видалити цю відповідь?',
                                                ],
                                            ]) ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-1" style="white-space: pre-wrap;"><?= Html::encode($r->content) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <hr class="my-4">

        <h3 id="comment-form-title" class="h5 mb-3">Додати коментар</h3>

        <?php $form = ActiveForm::begin([
            'action' => ['post/comment', 'slug' => $model->slug],
            'method' => 'post',
        ]); ?>

        <?= $form->field($newComment, 'parent_id')->hiddenInput(['id' => 'comment-parent-id'])->label(false) ?>

        <?= $form->field($newComment, 'author_name')->textInput(['id' => 'comment-author-name', 'maxlength' => true]) ?>
        <?= $form->field($newComment, 'author_email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($newComment, 'content')->textarea(['rows' => 4]) ?>

        <div class="d-flex gap-2">
            <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            <?= Html::button('Cancel reply', [
                'class' => 'btn btn-outline-secondary',
                'type' => 'button',
                'onclick' => "document.getElementById('comment-parent-id').value=''; document.getElementById('comment-form-title').innerText='Додати коментар';",
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
