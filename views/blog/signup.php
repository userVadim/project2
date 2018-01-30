<?php
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title="Sign Up";
?>

<div class="signup-div">
    <?php $form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']])?>
    <?= $form->field($model, 'username')->textInput() ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'email')->textInput() ?>
    <?= $form->field($photo, 'user_photo')->fileInput(['class'=>'btn btn-primary']) ?>
    <?= Html::submitButton('Sign Up!',['class'=>'btn btn-success']) ?>
    <?php $form= ActiveForm::end() ?>
</div>