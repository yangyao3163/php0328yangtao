<?php
$form = \yii\bootstrap\ActiveForm::begin();//表单开始
echo $form->field($model,"username")->textInput();
echo $form->field($model,"password_hash")->passwordInput();
echo $form->field($model,"email")->textInput();
echo $form->field($model,'role')->checkboxList(
    \yii\helpers\ArrayHelper::map(\Yii::$app->authManager->getRoles(),'name','name')
);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//表单结束