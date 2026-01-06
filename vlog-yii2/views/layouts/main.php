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
<?php $q = trim((string)Yii::$app->request->get('q', '')); ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'options' => ['class' => 'navbar navbar-expand-md navbar-dark custom-navbar'],
        'innerContainerOptions' => [
            'class' => 'container position-relative'
        ],
    ]);
    
?>
        <a class="navbar-brand-left d-flex align-items-center" href="<?= Yii::$app->homeUrl ?>">
        <img src="<?= Yii::getAlias('@web/images/Logo.png') ?>" alt="Logo">
    </a>

    <a class="navbar-brand navbar-brand-center" href="<?= Yii::$app->homeUrl ?>">
        TechHouse
    </a>

    <?php
    $menuItems = [];

        if (!Yii::$app->user->isGuest && (int)Yii::$app->user->identity->is_admin === 1) {
            $menuItems[] = ['label' => 'Admin', 'url' => ['/admin/index']];
        }

        $menuItems[] = Yii::$app->user->isGuest
            ? ['label' => 'Login', 'url' => ['/site/login']]
            : '<li class="nav-item">'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'nav-link btn btn-link logout']
                )
                . Html::endForm()
                . '</li>';

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'items' => array_filter($menuItems),
        ]);

    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container-fluid px-0">
        <div class="row g-0">

            <aside class="col-12 col-md-3 col-xl-2 sidebar">
                <div class="p-3 p-lg-4 h-100"> <div class="card sidebar-search-card mb-3">
                        <div class="card-body">
                            <div class="sidebar-search-title">Пошук</div>

                            <?= Html::beginForm(['post/index'], 'get', ['class' => 'sidebar-search-form']) ?>
                                <?= Html::textInput('q', $q, [
                                    'class' => 'form-control sidebar-search-input',
                                    'placeholder' => 'Назва або текст…',
                                    'autocomplete' => 'off',
                                ]) ?>
                                
                                <button type="submit" class="btn btn-primary sidebar-search-btn">
                                    <i class="bi bi-search"></i>
                                </button>

                                <?php if ($q !== ''): ?>
                                    <?= Html::a('<i class="bi bi-x"></i>', ['post/index'], [
                                        'class' => 'btn btn-outline-secondary sidebar-search-clear',
                                        'title' => 'Очистити',
                                    ]) ?>
                                <?php endif; ?>
                            <?= Html::endForm() ?>
                        </div>
                    </div>

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

                </div> 
            </aside>

            <div class="col-12 col-md-9 col-xl-10 content-area">
                <div class="p-3 p-lg-4">
                    <?php if (!empty($this->params['breadcrumbs'])): ?>
                        <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                    <?php endif ?>
                    <?= Alert::widget() ?>
                    <?= $content ?>
                </div>
            </div>

        </div>
    </div>
</main>
<!-- All Categories Modal -->
<div class="modal fade" id="allCategoriesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Всі категорії</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="catFilter" type="text" class="form-control mb-3" placeholder="Пошук категорій...">

                <div class="list-group" id="catList">
                    <?= Html::a(
                        'Усі пости',
                        ['post/index'],
                        ['class' => 'list-group-item list-group-item-action' . (($activeCategorySlug === null && $activeTagSlug === null) ? ' active' : '')]
                    ) ?>

                    <?php foreach ($categories as $cat): ?>
                        <?= Html::a(
                            Html::encode($cat->name),
                            ['category/view', 'slug' => $cat->slug],
                            [
                                'class' => 'list-group-item list-group-item-action' . ($activeCategorySlug === $cat->slug ? ' active' : ''),
                                'data-name' => mb_strtolower($cat->name),
                            ]
                        ) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="allTagsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Всі теги</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="tagFilter" type="text" class="form-control mb-3" placeholder="Пошук тегів...">

                <div class="d-flex flex-wrap gap-2" id="tagList">
                    <?php foreach ($tags as $tag): ?>
                        <?= Html::a(
                            Html::encode($tag->name),
                            ['tag/view', 'slug' => $tag->slug],
                            [
                                'class' => 'badge text-bg-' . ($activeTagSlug === $tag->slug ? 'primary' : 'secondary') . ' text-decoration-none tag-pill',
                                'data-name' => mb_strtolower($tag->name),
                            ]
                        ) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function bindFilter(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        if (!input || !container) return;

        input.addEventListener('input', function () {
            const q = (input.value || '').trim().toLowerCase();
            const items = container.querySelectorAll('[data-name]');
            items.forEach(el => {
                const name = (el.getAttribute('data-name') || '');
                el.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }

    bindFilter('catFilter', 'catList');
    bindFilter('tagFilter', 'tagList');
})();
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
