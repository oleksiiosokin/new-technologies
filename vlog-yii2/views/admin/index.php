<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Admin';
?>

<div class="card">
    <div class="card-body">
        <h1 class="h4 mb-3">Admin panel</h1>

        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('Posts', ['admin-post/index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Categories', ['admin-category/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('Tags', ['admin-tag/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>
</div>
