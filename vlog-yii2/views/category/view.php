<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Category $category */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = $category->name;

$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        <div class="text-muted">Категорія</div>
    </div>
    <?= Html::a('← Усі пости', ['post/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
</div>

<?php if (empty($models)): ?>
    <div class="card">
        <div class="card-body text-muted">У цій категорії поки немає постів.</div>
    </div>
<?php else: ?>
    <?php foreach ($models as $post): ?>
        <div class="card post-card mb-3">
            <div class="card-body">
                <h2 class="h4 post-title mb-1">
                    <?= Html::a(
                        Html::encode($post->title),
                        ['post/view', 'slug' => $post->slug],
                        ['class' => 'text-decoration-none']
                    ) ?>
                </h2>

                <div class="post-meta mb-2">
                    <?= Html::encode(date('Y-m-d H:i', (int)($post->published_at ?? $post->created_at))) ?>
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
