<?=\yii\bootstrap\Html::a("添加",["goods-category/add"],["class"=>"btn btn-info btn-sm"])?>
    <table class="table table-bordered table-condensed">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>上级分类ID</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach($brand as $v): ?>
            <tr>
                <td><?=$v->id ?></td>
                <td><?=$v->name?></td>
                <td><?=$v->parent_id?></td>
                <td><?=$v->intro?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                    <?=\yii\bootstrap\Html::a('删除',['goods-category/del','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);
