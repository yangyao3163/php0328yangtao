<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'menu_name')->textInput();
echo $form->field($model,'superior_menu')->dropDownList(\backend\models\Menu::getPidOptions());
echo $form->field($model,'menu_url')->dropDownList(\backend\models\Menu::getUrlOptions(),['prompt'=>'请选择']);
echo $form->field($model,"sort")->textInput(['type'=>'number']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn bth-info']);
\yii\bootstrap\ActiveForm::end();
