<?php

use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div class="create-item">
    <?php $form= ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]) ?>
    <?= $form->field($blogItem, 'title')->textInput() ?>
    <?= $form->field($blogItem, 'content')->textarea() ?>
    <?= $form->field($images, 'content_images[]')->fileInput(['multiple'=>true]) ?>
    <?= $form->field($blogItem, 'comments')->dropDownList([
        0=>'off',
        1=>'on',
    ]) ?>
    <?= Html::submitButton('Create'); ?>
    <?php $form= ActiveForm::end() ?>
</div>
