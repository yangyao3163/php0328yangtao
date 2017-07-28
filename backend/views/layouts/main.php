<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
//    $menuItems = [
//        ['label'=>'品牌管理','items'=>[
//            ['label'=>'添加品牌','url'=>['brand/add']],
//            ['label'=>'品牌列表','url'=>['brand/index']],
//        ]],
//        ['label'=>'商品管理','items'=>[
//            ['label'=>'添加商品','url'=>['goods/add']],
//            ['label'=>'商品列表','url'=>['goods/index']],
//            ['label'=>'添加商品分类','url'=>['goods-category/add']],
//            ['label'=>'商品分类列表','url'=>['goods-category/index']],
//        ]],
//        ['label'=>'文章管理','items'=>[
//            ['label'=>'添加文章','url'=>['article/add']],
//            ['label'=>'文章列表','url'=>['article/index']],
//            ['label'=>'文章分类添加','url'=>['article-category/add']],
//            ['label'=>'文章分类列表','url'=>['article-category/index']],
//        ]],
//        ['label'=>'权限管理','items'=>[
//            ['label'=>'添加权限','url'=>['rbac/add-permission']],
//            ['label'=>'权限列表','url'=>['rbac/permission-index']],
//            ['label'=>'角色添加','url'=>['rbac/add-role']],
//            ['label'=>'角色列表','url'=>['rbac/role-index']],
//        ]],
//        ['label' => '修改密码', 'url' => ['/user/ch-pw']],
//    ];
    $menuItems = [];
    $menus = \backend\models\Menu::findAll(['superior_menu'=>0]);
    foreach($menus as $menu){
        //一级菜单
        $items = [];
        foreach ($menu->children as $child){
            //判断用户访问权限
            if(Yii::$app->user->can($child->menu_url)){
                $items[] =  ['label'=>$child->menu_name,'url'=>[$child->menu_url]];
            }
        }
        //没有子菜单时不显示一级分类
        if(!empty($items)){
            $menuItems[] = ['label' => $menu->menu_name, 'items' => $items];
        }
    }

    if (Yii::$app->user->isGuest) {
        $menuItems = [];
        $menuItems[] = ['label' => '登陆', 'url' => ['/user/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/user/logout'], 'post')
            . Html::submitButton(
                '注销 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();

//
//    NavBar::begin([
//        'brandLabel' => '首页',
//        'brandUrl' => Yii::$app->homeUrl,
//        'options' => [
//            'class' => 'navbar-inverse navbar-fixed-top',
//            'id' => 'menu-top',
//        ],
//        //'brandOptions' => ['class' => 'fa fa-flag fa-2x pull-left'],
//
//    ]);
//
//    if (Yii::$app->user->isGuest) {
//        $menuItems[] = ['label' => '登陆', 'url' => ['/user/login']];
//    } else {
////循环出菜单栏
//        if(isset($nav)){
//            for($n=0;$n<count($nav);$n++){
//                $_v = explode('|',$nav[$n]);
//                $menuItems[] = [
//                    'label' => $_v[1],
//                    'url' => ["/$_v[2]/default/index",'id'=>$_v[0],'en'=>$_v[2]],
//                    //'linkOptions' => ['class' => 'active'],
//                    //'options' => ["id"=>"_M$n"],
//                ];
//
//            }
//        }
//            $menuItems[] = '<li>'
//            . Html::beginForm(['/user/logout'], 'post')
//            . Html::submitButton(
//                '注销 (' . Yii::$app->user->identity->username . ')',
//                ['class' => 'btn btn-link logout']
//            )
//            . Html::endForm()
//            . '</li>';
//    }
//    echo Nav::widget([
//        'options' => ['class' => 'navbar-nav navbar-right'],
//        'items' => $menuItems,
//
//    ]);
//    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
