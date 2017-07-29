<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;
use backend\filters\RbacFilter;

class ArticleCategoryController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = ArticleCategory::find()->where(['!=','status','-1'])->orderBy('sort desc');
        //总条数
        $total = $query->count();
        //每页显示条数
        $perPage = 2;
        //分页工具条
        $pager = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);
        //每页显示几条。。

        $brand = $query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render("index",["article"=>$brand,'pager'=>$pager]);
    }

    //添加
    public function actionAdd(){
        //实例化模型
        $model = new ArticleCategory();
        $request = new Request();
        //判断POST提交
        if($request->isPost) {
            //加载表单模型
            $model->load($request->post());
            //验证数据
            if ($model->validate()) {

                $model->save();
                \Yii::$app->session->setFlash("success","添加成功");
                return $this->redirect(["article-category/index"]);
            }else{
                var_dump($model->getErrors());//错误信息
                exit;
            }
        }
        //默认选择显示
        $model->status=1;
        //如果不是post提交，调用视图
        return $this->render("add",["model"=>$model]);
    }

    //修改
    public function actionEdit($id){
        //实例化模型
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = new Request();
        //判断POST提交
        if($request->isPost) {
            //加载表单模型
            $model->load($request->post());
            //验证数据
            if ($model->validate()) {

                $model->save();
                \Yii::$app->session->setFlash("warning","修改成功");
                return $this->redirect(["article-category/index"]);
            }else{
                var_dump($model->getErrors());//错误信息
                exit;
            }
        }
        //如果不是post提交，调用视图
        return $this->render("add",["model"=>$model]);
    }

    public function actionDel($id)
    {
        $model=ArticleCategory::findOne(['id'=>$id]);
        $model->status= -1;
        $model->save(false);
        //提示
        \Yii::$app->session->setFlash("danger","删除成功");
        return $this->redirect(['article-category/index']);
    }


    //行为
    public function behaviors()
    {
        return [
            'rbac' => [
                'class' => RbacFilter::className(),
            ]
        ];

    }
}
