<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'rememberMe')->checkbox(['label'=>'记住密码']);

echo \yii\bootstrap\Html::submitButton('登录',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();