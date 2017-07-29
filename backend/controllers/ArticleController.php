<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use backend\models\Article;
use yii\web\Request;

class ArticleController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Article::find()->where(['!=', 'status', '-1'])->orderBy('sort desc');
        //总条数
        $total = $query->count();
        //每页显示条数
        $perPage = 2;
        //分页工具条
        $pager = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => $perPage
        ]);
        //每页显示几条
        $article = $query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render("index", ["article" => $article, 'pager' => $pager]);
    }

    //添加
    public function actionAdd()
    {
        //实例化模型
        $model = new Article();
        $model2 = new ArticleDetail();
        $request = new Request();
        //判断POST提交
        if ($request->isPost) {
            //加载表单模型
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            if ($model->validate() && $model2->validate()) {
                $model->create_time = time();
                $model->save();

                $model2->article_id = $model->id;
                $model2->save();
                \Yii::$app->session->setFlash("success", "添加成功");
                return $this->redirect(["article/index"]);
            } else {
                var_dump($model->getErrors());//错误信息
                exit;
            }
        }
        //默认选择显示
        $model->status = 1;
        //如果不是post提交，调用视图
        return $this->render("add", ["model" => $model, "model2" => $model2]);
    }

    //修改
    public function actionEdit($id)
    {
        //实例化模型
        $model = Article::findOne(['id' => $id]);
        $model2 = ArticleDetail::findOne(['article_id' => $id]);
        $request = new Request();
        //判断POST提交
        if ($request->isPost) {
            //加载表单模型
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            if ($model->validate() && $model2->validate()) {
                $model->save();
                $model2->article_id = $model->id;
                $model2->save();
                \Yii::$app->session->setFlash("success", "修改成功");
                return $this->redirect(["article/index"]);
            } else {
                var_dump($model->getErrors());//错误信息
                exit;
            }
        }
        //如果不是post提交，调用视图
        return $this->render("add", ["model" => $model, "model2" => $model2]);
    }

    //删除
    public function actionDel($id)
    {
        $model = Article::findOne(['id' => $id]);
        $model->status = -1;
        $model->save(false);
        //提示
        \Yii::$app->session->setFlash("danger", "删除成功");
        return $this->redirect(['article/index']);
    }

    public function behaviors()
    {
        return [
            'rbac' => [
                'class' => RbacFilter::className(),
            ]
        ];

    }
}
