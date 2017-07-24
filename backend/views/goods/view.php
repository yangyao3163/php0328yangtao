<h1><?=$model->name?></h1>
<?=\yii\bootstrap\Carousel::widget([
    'items' => $model->getPics()
]);?>
<div class="container">
    <?=$model->goodsIntro->content?>
</div>