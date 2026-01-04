<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCssFile('@web/css/blog.css', ['depends' => [\app\assets\AppAsset::class]]);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$categories = $this->params['categories'] ?? [];
$tags = $this->params['tags'] ?? [];
$activeCategorySlug = $this->params['activeCategorySlug'] ?? null;
$activeTagSlug = $this->params['activeTagSlug'] ?? null;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [


            Yii::$app->user->isGuest
                ? ['label' => 'Login', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container my-4">
        <div class="row">
            <aside class="col-12 col-lg-3 mb-4">
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Категорії</div>
                    <div class="list-group list-group-flush">
                        <?= Html::a(
                            'Усі пости',
                            ['post/index'],
                            ['class' => 'list-group-item list-group-item-action' . (($activeCategorySlug === null && $activeTagSlug === null) ? ' active' : '')]
                        ) ?>

                        <?php foreach ($categories as $cat): ?>
                            <?= Html::a(
                                Html::encode($cat->name),
                                ['category/view', 'slug' => $cat->slug],
                                ['class' => 'list-group-item list-group-item-action' . ($activeCategorySlug === $cat->slug ? ' active' : '')]
                            ) ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-semibold">Теги</div>
                    <div class="card-body">
                        <?php if (empty($tags)): ?>
                            <div class="text-muted">Нема тегів</div>
                        <?php else: ?>
                            <?php foreach ($tags as $tag): ?>
                                <?= Html::a(
                                    Html::encode($tag->name),
                                    ['tag/view', 'slug' => $tag->slug],
                                    ['class' => 'badge text-bg-' . ($activeTagSlug === $tag->slug ? 'primary' : 'secondary') . ' me-1 mb-1 text-decoration-none']
                                ) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>

            <div class="col-12 col-lg-9">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                <?php endif ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</main>



<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
