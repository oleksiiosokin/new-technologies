<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
?>
<?php $q = $searchModel->q ?? ''; ?>

<?php if (empty($models)): ?>
    <div class="card">
        <div class="card-body text-muted">Поки що немає постів.</div>
    </div>
<?php else: ?>
    <?php foreach ($models as $post): ?>
        <div class="card post-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="flex-grow-1">
                        <?php if (!empty($post->image_path)): ?>
                        <div class="post-cover-wrap list-cover mb-3">
                            <?= Html::img('/' . ltrim($post->image_path, '/'), [
                                'alt' => $post->title,
                                'class' => 'img-fluid post-cover',
                            ]) ?>
                        </div>
                    <?php endif; ?>
                        <h2 class="h4 post-title mb-1">
                            <?= Html::a(
                                Html::encode($post->title),
                                ['post/view', 'slug' => $post->slug],
                                ['class' => 'text-decoration-none']
                            ) ?>
                        </h2>

                        <?php
                        $publishedTs = (int)($post->published_at ?: $post->created_at);
                        $updatedTs   = (int)($post->updated_at ?: 0);
                        $createdTs   = (int)($post->created_at ?: 0);

                        $showUpdated = $updatedTs && $createdTs && $updatedTs > $createdTs;
                        ?>
                        <div class="post-meta mb-2">
                            <span><strong>Опубліковано:</strong> <?= Html::encode(date('d-m-Y H:i', $publishedTs)) ?></span>

                            <?php if ($showUpdated): ?>
                                <span class="ms-3"><strong>Оновлено:</strong> <?= Html::encode(date('d-m-Y H:i', $updatedTs)) ?></span>
                            <?php endif; ?>

                            <span class="mx-2">·</span>
                                
                            <?= Html::a(
                                Html::encode($post->category?->name ?? '—'),
                                $post->category ? ['category/view', 'slug' => $post->category->slug] : ['post/index'],
                                ['class' => 'text-decoration-none']
                            ) ?>
                        </div>


                        <p class="mb-3">
                            <?= Html::encode(StringHelper::truncate(strip_tags($post->content), 220)) ?>
                        </p>

                        <?php if ($post->tags): ?>
                            <div class="mb-3">
                                <?php foreach ($post->tags as $tag): ?>
                                    <?= Html::a(
                                        Html::encode($tag->name),
                                        ['tag/view', 'slug' => $tag->slug],
                                        ['class' => 'badge text-bg-secondary me-1 mb-1 text-decoration-none']
                                    ) ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?= Html::a(
                            'Read more →',
                            ['post/view', 'slug' => $post->slug],
                            ['class' => 'btn btn-sm btn-outline-secondary btn-read']
                        ) ?>
                    </div>

                    
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?= LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'pagination justify-content-center mt-4'],
        'linkOptions' => ['class' => 'page-link'],
        'pageCssClass' => 'page-item',
        'activePageCssClass' => 'active',
        'disabledPageCssClass' => 'disabled',
        'prevPageLabel' => '«',
        'nextPageLabel' => '»',
    ]) ?>
<?php endif; ?>
