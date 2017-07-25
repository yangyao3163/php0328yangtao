<?php
$form = \yii\bootstrap\ActiveForm::begin();//表单开始
echo $form->field($model,"oldPassword")->passwordInput();
echo $form->field($model,"password")->passwordInput();
echo $form->field($model,"rePassword")->passwordInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//表单结束