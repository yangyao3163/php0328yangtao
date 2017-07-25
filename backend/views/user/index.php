<?=\yii\bootstrap\Html::a("添加",["user/add"],["class"=>"btn btn-info btn-sm"])?>
    <table class="table table-bordered table-condensed">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>邮箱</th>
            <th>最后登陆时间</th>
            <th>最后登陆IP</th>
            <th>操作</th>
        </tr>
        <?php foreach($model as $v): ?>
            <tr>
                <td><?=$v->id ?></td>
                <td><?=$v->username?></td>
                <td><?=$v->email?></td>
                <td><?=date('Y-m-d H:i:s',$v->last_login_time)?></td>
                <td><?=long2ip($v->last_login_ip)?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('修改',['user/edit','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                    <?=\yii\bootstrap\Html::a('删除',['user/del','id'=>$v->id],['class'=>'btn btn-sm btn-success'])?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']);
