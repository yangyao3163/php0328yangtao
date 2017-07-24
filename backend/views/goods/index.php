<?php
$form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    //get方式提交,需要显式指定action
    'action'=>\yii\helpers\Url::to(['goods/index']),
    'layout'=>'inline'
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($model,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'￥'])->label('-');
echo \yii\bootstrap\Html::submitButton('<span class="glyphicon glyphicon-search"></span>搜索',['class'=>'btn btn-default']);
\yii\bootstrap\ActiveForm::end();
?>

<?=\yii\bootstrap\Html::a("添加",["goods/add"],["class"=>"btn btn-info btn-sm"])?>
<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>商品名称</th>
        <th>货号</th>
        <th>LOGO图片</th>
        <th>商品价格</th>
        <th>状态</th>
        <th>库存</th>
        <th>排序</th>
        <th>是否在售</th>
        <th>操作</th>
    </tr>
    <?php foreach($goods as $good): ?>
        <tr>
            <td><?=$good->id ?></td>
            <td><?=$good->name?></td>
            <td><?=$good->sn?></td>
            <td><?= \yii\helpers\Html::img($good->logo,["height"=>30])?></td>
            <td><?=$good->shop_price?></td>
            <td><?=\backend\models\Goods::getStatusOptions()[$good->status]?></td>
            <td><?=$good->stock?></td>
            <td><?=$good->sort?></td>
            <td><?=\backend\models\Goods::$sale_options[$good->is_on_sale]?></td>
            <td>
                <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-film"></span>预览',['view','id'=>$good->id],['class'=>'btn btn-success'])?>
                <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-picture"></span>相册',['gallery','id'=>$good->id],['class'=>'btn btn-default'])?>
                <?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$good->id],['class'=>'btn btn-sm btn-success'])?>
                <?=\yii\bootstrap\Html::a('删除',['goods/del','id'=>$good->id],['class'=>'btn btn-sm btn-success'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget(["pagination"=>$pager,"nextPageLabel"=>"下一页","prevPageLabel"=>"上一页","firstPageLabel"=>"首页"]);