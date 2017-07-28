<?=\yii\bootstrap\Html::a("添加",["menu/add"],["class"=>"btn btn-info btn-sm"])?>
    <table class="table table-bordered table-condensed">
        <tr>
            <th>ID</th>
            <th>菜单名称</th>
            <th>菜单地址</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
        <?php foreach($model as $v): ?>
            <tr>
                <td><?=$v->id ?></td>
                <td><?=$v->menu_name?></td>
                <td><?=$v->menu_url?></td>
                <td><?=$v->sort?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                    <?=\yii\bootstrap\Html::a('删除',['menu/del','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);
