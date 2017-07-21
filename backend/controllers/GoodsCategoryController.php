<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\web\HttpException;
use yii\data\Pagination;

class GoodsCategoryController extends \yii\web\Controller
{
    //分类列表展示
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = GoodsCategory::find();
        //总条数
        $total = $query->count();
        //每页显示条数
        $perPage = 2;
        //分页工具条
        $pager = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);
        //每页显示几条
        $brand = $query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render("index",["brand"=>$brand,'pager'=>$pager]);
    }

    //添加商品分类
    public function actionAdd(){
        $model = new GoodsCategory();
        //判断是否是POST提交，并验证
        if($model->load(\yii::$app->request->post()) && $model->validate()){
            //判断分类是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException('404','上级分类不存在');
                }

            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash("success","分类添加成功");
            return $this->redirect(['index']);
        }
        return $this->render('add',['model'=>$model]);
    }

    //添加商品分类-ztree
    public function actionAdd2(){
        $model = new GoodsCategory(['parent_id'=>0]);
        //判断是否是POST提交，并验证
        if($model->load(\yii::$app->request->post()) && $model->validate()){
            //判断分类是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException('404','上级分类不存在');
                }

            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash("success","分类添加成功");
            return $this->redirect(['index']);
        }
        //获取所有分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();

        return $this->render('add2',['model'=>$model,'categories'=>$categories]);
    }

    //修改商品分类-ztree
    public function actionEdit($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        //判断是否是POST提交，并验证
        if($model->load(\yii::$app->request->post()) && $model->validate()){
            //判断分类是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException('404','上级分类不存在');
                }

            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash("success","分类修改成功");
            return $this->redirect(['index']);
        }
        //获取所有分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();

        return $this->render('add2',['model'=>$model,'categories'=>$categories]);
    }

    //分类删除  判断是否有子分类
    public function actionDel($id)
    {
        $model=GoodsCategory::findOne(['id'=>$id]);
        //查询出当前ID=子类含有的父ID的数据
        $mode = GoodsCategory::findOne(['parent_id'=>$id]);
        if($mode){
            \Yii::$app->session->setFlash("danger","有父类，分类删除失败");
        }else{
            $model->delete();
            \Yii::$app->session->setFlash("success","分类删除成功");
        }
        return $this->redirect(['goods-category/index']);
    }


    /*//测试嵌套集合的用法
    public function actionTest(){
        //创建一个根节点
//        $category = new GoodsCategory();
//        $category->name = "家用电器";
//        $category->makeRoot();

        //创建子节点
//        $category2 = new GoodsCategory();
//        $category2->name = "小家电";
//        $category = GoodsCategory::findOne(['id'=>1]);
//        $category2->parent_id = $category->id;
//        $category2->prependTo($category);

        //删除节点
//        $cate = GoodsCategory::findOne(['id'=>3])->delete();
        echo "操作成功";
    }*/

    //测试ztree
    public function actionZtree(){
        //不加载布局文件
        return $this->renderPartial('ztree');
    }

}
