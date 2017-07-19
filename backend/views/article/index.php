<?=\yii\bootstrap\Html::a("添加",["article/add"],["class"=>"btn btn-info btn-sm"])?>
    <table class="table table-bordered table-condensed">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>简介</th>
            <th>文章分类id</th>
            <th>排序</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php foreach($article as $v): ?>
            <tr>
                <td><?=$v->id ?></td>
                <td><?=$v->name?></td>
                <td><?=$v->intro?></td>
                <td><?=$v->articleCategory->name?></td>
                <td><?=$v->sort?></td>
                <td><?=\backend\models\Brand::getStatusOptions(false)[$v->status]?></td>
                <td><?=date('Y-m-d H:i:s',$v->create_time)?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                    <?=\yii\bootstrap\Html::a('删除',['article/del','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);
