<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Category $category */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Category: ' . $category->name;

$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (empty($models)): ?>
    <p>No posts in this category.</p>
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
            </div>

            <p><?= Html::encode(StringHelper::truncate(strip_tags($post->content), 220)) ?></p>
        </article>
    <?php endforeach; ?>

    <?= LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'pagination'],
        'linkOptions' => ['class' => 'page-link'],
        'pageCssClass' => 'page-item',
        'activePageCssClass' => 'active',
        'disabledPageCssClass' => 'disabled',
        'prevPageLabel' => '«',
        'nextPageLabel' => '»',
    ]) ?>
<?php endif; ?>
