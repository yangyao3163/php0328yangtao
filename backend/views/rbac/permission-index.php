<?=\yii\bootstrap\Html::a("添加",["rbac/add-permission"],["class"=>"btn btn-info btn-sm"])?>
<table class="table">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model): ?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['edit-permission','name'=>$model->name])?>
                <?=\yii\bootstrap\Html::a('删除',['del-permission','name'=>$model->name])?>

            </td>
        </tr>
    <?php endforeach; ?>

</table>
