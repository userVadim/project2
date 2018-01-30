<?php

use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>

<div class="create-item">
    <?php $form= ActiveForm::begin(['options'=>['enctype'=>'multipart/formdata']]) ?>
    <?= $form->field($item, 'title')->textInput() ?>
    <?= $form->field($item, 'content')->textarea() ?>

    <?= $form->field($item, 'comments')->dropDownList([
        0=>'off',
        1=>'on',
    ]) ?>
    <?= Html::submitButton('Update'); ?>
    <?php $form= ActiveForm::end() ?>
</div>
