<?php
$form = \yii\bootstrap\ActiveForm::begin();//表单开始
echo $form->field($model,"name")->textInput();
echo $form->field($model,"intro")->textarea();
echo $form->field($model,"sort")->textInput(['type'=>'number']);
echo $form->field($model,"status",['inline'=>true])->radioList(\backend\models\Brand::getStatusOptions());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//表单结束