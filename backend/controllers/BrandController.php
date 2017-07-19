<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;


class BrandController extends \yii\web\Controller
{
    //列表
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = Brand::find()->where(['!=','status','-1'])->orderBy('sort desc');
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

    /*//添加
    public function actionAdd(){
        //实例化模型
        $model = new Brand();
        $request = new Request();
        //判断post提交
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());//必须写非空的验证，才能加载数据
            //实例化文件上传对象
//            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //处理图片
                //有文件上传
//                if($model->imgFile){
//                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
//                    if(!is_dir($d)){
//                        mkdir($d);
//                    }
//                    //创建文件夹
//                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
//                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
//                    $model->logo = $fileName;
//                }
                //通过验证，保存到数据表中
                $model->save();
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
    }*/

    public function actionAdd(){
        //实例化模型
        $model = new Brand();
        //判断post提交，加载表单数据
        if($model->load(\Yii::$app->request->post())&& $model->validate()){
            $model->save();
            //提示
            \Yii::$app->session->setFlash("success","品牌添加成功");
            //跳转
            return $this->redirect(["brand/index"]);
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }



    /*//修改
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
    }*/

    //修改
    public function actionEdit($id){
        //实例化模型,根据ID来修改
        $model = brand::findOne(['id'=>$id]);
        //判断post提交，加载表单数据
        if($model->load(\Yii::$app->request->post())&& $model->validate()){
            $model->save();
            //提示
            \Yii::$app->session->setFlash("success","品牌修改成功");
            //跳转
            return $this->redirect(["brand/index"]);
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



    public function actions() {
        return [
            //接收
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                //启用跨站请求攻击验证
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                //如果文件已经存在，是否覆盖
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                //文件的保存方式
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                //图片格式，图片最大不超过1M
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //输出文件的相对路径
                    //$action->output['fileUrl'] = $action->getWebUrl();
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云
                    //实例化七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    //调上传方法 第一个为文件绝对路径 第二个为相对路径
                    $qiniu->uploadFile(
                        $action->getSavePath(),
                        $action->getWebUrl()
                    );
                    //获取该图片在七牛云的地址
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl'] = $url;


                },
            ],
        ];
    }

    //测试七牛云上传功能
    public function actionQiniu(){
        $config = [
            //七牛云密钥 账号 密码 对应的数据存储名称
            'accessKey'=>'mIV2i8qyVH8R3xbqitMHTw4-x-Ok0o6dgbfoJS8c',
            'secretKey'=>'i9OQJC33bq-fpC5SPKWUuRxjVq7YTyoyGe7GCd3t',
            'domain'=>'http://otbozhzoq.bkt.clouddn.com/',
            'bucket'=>'0328php',
            'area'=>Qiniu::AREA_HUADONG
        ];

        $qiniu = new Qiniu($config);
        $key = time();

        //将图片上传到七牛云
        $qiniu->uploadFile($_FILES['tmp_name'],$key);
        //获取该图片在七牛云的地址
        $url = $qiniu->getLink($key);
    }

}
