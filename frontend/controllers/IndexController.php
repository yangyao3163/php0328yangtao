<?php
namespace frontend\controllers;


use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsIntro;
use yii\web\Controller;

class IndexController extends Controller{

    public $layout=false;

    //首页
    public function actionIndex(){
        $model=GoodsCategory::find()->where(['=','parent_id',0])->all();
        return $this->render('index',['model'=>$model]);
    }
    //分类
    public function actionList(){
        $query = Goods::find();
        if($id=\Yii::$app->request->get('id')){
            $id = \Yii::$app->request->get('id');
            $cate = GoodsCategory::findOne(['id'=>$id]);
            //判断是否有子类
            if($cate->rgt-$cate->lft>1){
                //查询出子类
                $chilh = GoodsCategory::find()->andWhere(['<','lft',$cate->rgt])->andWhere(['>','lft',$cate->lft])->andWhere(['=','depth',2])->all();

                foreach($chilh as $c){
                    $query->orWhere(['=','goods_category_id',$c->id]);
                }
            }else{
                $query->andWhere(['=','goods_category_id',$id]);
            }
        }
        $model=$query->all();
        return $this->render('list',['model'=>$model]);
    }

    //详情页
    public function actionGoods($id){
        $goods=Goods::findOne(['id'=>$id]);
        return $this->render('goods',['goods'=>$goods]);
    }
}
