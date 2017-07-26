<h1><?=$model->scenario==\backend\models\PermissionForm::SCENARIO_ADD?'添加':'修改'?>管理</h1>
<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput(['readonly'=>$model->scenario!=\backend\models\PermissionForm::SCENARIO_ADD]);
echo $form->field($model,'description');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn bth-info']);
\yii\bootstrap\ActiveForm::end();