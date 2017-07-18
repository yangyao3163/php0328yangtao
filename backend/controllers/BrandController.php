<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Brand::find()->where(['!=','status','-1']);
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

    //添加
    public function actionAdd(){
        //实例化模型
        $model = new Brand();
        $request = new Request();
        //判断post提交
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());//必须写非空的验证，才能加载数据
            //实例化文件上传对象
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //处理图片
                //有文件上传
                if($model->imgFile){
                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    //创建文件夹
                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                    $model->logo = $fileName;
                }
                //通过验证，保存到数据表中
                $model->save(false);
                //提示
                \Yii::$app->session->setFlash("success","品牌添加成功");
                return $this->redirect(["brand/index"]);
            }else{
                //错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }



    //修改
    public function actionEdit($id){
        //实例化模型
        $model = brand::findOne(['id'=>$id]);
        $request = new Request();
        //判断post提交
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());//必须写非空的验证，才能加载数据
            //实例化文件上传对象
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //处理图片
                //有文件上传
                if($model->imgFile){
                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    //创建文件夹
                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                    $model->logo = $fileName;
                }
                //通过验证，保存到数据表中
                $model->save(false);
                //提示
                \Yii::$app->session->setFlash("warning","品牌修改成功");
                return $this->redirect(["brand/index"]);
            }else{
                //错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }

    public function actionDel($id)
    {
        $model=Brand::findOne(['id'=>$id]);
        $model->status= -1;
        $model->save(false);
        //提示
        \Yii::$app->session->setFlash("danger","品牌删除成功");
        return $this->redirect(['brand/index']);
    }

}
