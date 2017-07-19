<?php
$form = \yii\bootstrap\ActiveForm::begin();//表单开始
echo $form->field($model,"name")->textInput();
echo  $form->field($model,"article_category_id")->dropDownList(\backend\models\Article::getAuthorOptions());
echo $form->field($model,"sort")->textInput(['type'=>'number']);
echo $form->field($model,"status",['inline'=>true])->radioList(\backend\models\Brand::getStatusOptions());
echo $form->field($model,"intro")->textarea();
echo $form->field($model2,"content")->textarea(['rows'=>10]);

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();//表单结束