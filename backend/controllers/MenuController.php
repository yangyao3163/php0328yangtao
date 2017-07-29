<?php

namespace backend\controllers;

use backend\models\Menu;
use yii\web\HttpException;
use yii\data\Pagination;
use backend\filters\RbacFilter;


class MenuController extends \yii\web\Controller
{
    //添加菜单
   public function actionAdd(){
       //实例化模型，顶级分类默认为1
       $model = new Menu();
       //判断post提交，加载表单数据
       if($model->load(\Yii::$app->request->post())&& $model->validate()){
           $model->save();
           //提示
           \Yii::$app->session->setFlash("success","添加成功");
           //跳转
           return $this->redirect(["menu/index"]);
       }
       //调用视图
       return $this->render('add',['model'=>$model]);
   }


    //菜单列表展示
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Menu::find();
        //总条数
        $total = $query->count();
        //每页显示条数
        $perPage = 10;
        //分页工具条
        $pager = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);
        //每页显示几条
        $model = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render("index",["model"=>$model,'pager'=>$pager]);
    }


    //分类删除  判断是否有子分类
    public function actionDel($id)
    {
        $model=Menu::findOne(['id'=>$id]);
        //查询出当前ID=子类含有的父ID的数据
        $mode = Menu::findOne(['superior_menu'=>$id]);
        if($mode){
            \Yii::$app->session->setFlash("danger","有子类，分类删除失败");
        }else{
            $model->delete();
            \Yii::$app->session->setFlash("success","分类删除成功");
        }
        return $this->redirect(['menu/index']);
    }

    //修改商品分类-ztree
    public function actionEdit($id)
    {
        $model = Menu::findOne(['id' => $id]);
        //查询出当前ID=子类含有的父ID的数据
        $prd = $model->superior_menu;
        //判断是否是POST提交，并验证
        if ($model->load(\yii::$app->request->post()) && $model->validate()) {
            //判断
            if ($model->superior_menu == $prd) {
                $model->save();
                \Yii::$app->session->setFlash("success", "分类修改成功");
            } else {
                \Yii::$app->session->setFlash("danger", "有子类，分类修改失败");
            }
            return $this->redirect(['index']);
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
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
