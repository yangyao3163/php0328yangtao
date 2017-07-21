<?php
//
//$form = \yii\bootstrap\ActiveForm::begin();
//echo $form->field($model,'name');
//echo $form->field($model,'parent_id');
//echo $form->field($model,'intro');
//
//echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//\yii\bootstrap\ActiveForm::end();

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'parent_id');
echo $form->field($model,'intro');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();