<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Blog';
$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (empty($models)): ?>
    <p>No posts yet.</p>
<?php else: ?>
    <?php foreach ($models as $post): ?>
        <article style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #ddd;">
            <h2 style="margin-top:0;">
                <?= Html::a(
                    Html::encode($post->title),
                    ['post/view', 'slug' => $post->slug]
                ) ?>
            </h2>

            <div style="opacity: .7; font-size: 14px;">
                <?= Html::encode(date('Y-m-d H:i', (int)($post->published_at ?? $post->created_at))) ?>
                · Category: <?= Html::encode($post->category?->name ?? '—') ?>
            </div>

            <?php if (!empty($post->image_path)): ?>
                <div style="margin: 10px 0;">
                    <?= Html::img('/' . ltrim($post->image_path, '/'), [
                        'alt' => $post->title,
                        'style' => 'max-width: 320px; height: auto; border-radius: 6px;',
                    ]) ?>
                </div>
            <?php endif; ?>

            <p>
                <?= Html::encode(StringHelper::truncate(strip_tags($post->content), 220)) ?>
            </p>

            <?php if ($post->tags): ?>
                <div style="margin-top: 8px;">
                    <?php foreach ($post->tags as $tag): ?>
                        <span style="display:inline-block; margin-right:6px; padding:2px 8px; border:1px solid #ccc; border-radius:999px; font-size:12px;">
                            <?= Html::encode($tag->name) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>

    <?= LinkPager::widget([
        'pagination' => $pagination,
        // щоб пагінація не виглядала як голі цифри:
        'options' => ['class' => 'pagination'],
        'linkOptions' => ['class' => 'page-link'],
        'pageCssClass' => 'page-item',
        'activePageCssClass' => 'active',
        'disabledPageCssClass' => 'disabled',
        'prevPageLabel' => '«',
        'nextPageLabel' => '»',
    ]) ?>
<?php endif; ?>
