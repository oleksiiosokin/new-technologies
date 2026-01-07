<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Category;

/** @var yii\web\View $this */
/** @var app\models\Post $model */
/** @var yii\widgets\ActiveForm $form */

$categories = ArrayHelper::map(
    Category::find()->orderBy('name')->all(),
    'id',
    'name'
);
?>

<div class="post-form">

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        $categories,
        ['prompt' => '— Обрати категорію —']
    ) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 8]) ?>
    
    <?= $form->field($model, 'tagsInput')
    ->textInput(['placeholder' => 'Напр: php, yii2, security'])
    ->hint('Теги вводяться через кому.') ?>


    <?= $form->field($model, 'status')->dropDownList([
        1 => 'Опубліковано',
        0 => 'Чернетка',
    ]) ?>

    <?= $form->field($model, 'published_at')->input('datetime-local', [
        'value' => $model->published_at
            ? date('Y-m-d\TH:i', $model->published_at)
            : '',
    ]) ?>

    <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>

    <?php if (!empty($model->image_path)): ?>
        <div class="mb-3" style="max-width:260px;">
            <img src="<?= $model->image_path ?>" class="img-fluid rounded border">
        </div>
    <?php endif; ?>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Створити' : 'Оновити',
            ['class' => 'btn btn-success']
        ) ?>
    </div>
    

<?php ActiveForm::end(); ?>

</div>
