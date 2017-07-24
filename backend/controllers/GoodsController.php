<?php
namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use yii\data\Pagination;
use yii\web\Controller;
use backend\models\GoodsGallery;
use flyok666\uploadifive\UploadAction;
use yii\web\NotFoundHttpException;

class GoodsController extends Controller{

    //列表展示
    public function actionIndex(){
        $model = new GoodsSearchForm();
        //根据条件查询数据
        $query = Goods::find()->where(['>','status','0']);
        //搜索
        $model->search($query);
        //得到总数
        $total = $query->count();
        //每页显示数量3
        $perPage = 3;
        $pager = new Pagination([
            "totalCount"=>$total,
            "defaultPageSize"=>$perPage,
        ]);
        //得到分页数据
        $goods = $query->limit($pager->limit)->offset($pager->offset)->all();
        //加载页面，返回数据
        return $this->render('index',['goods'=>$goods,"pager"=>$pager,'model'=>$model]);
    }

    //添加商品
    public function actionAdd(){
        //商品goods
        $model = new Goods();
        //商品详情
        $introModel = new GoodsIntro();
        if($model->load(\Yii::$app->request->post()) && $introModel->load(\Yii::$app->request->post())){
            if($model->validate() && $introModel->validate()){
                //自动生成sn，规则为年月日+今天的第几个商品,比如201704010001
                $day = date('Y-m-d');
                $goodsCount = GoodsDayCount::findOne(['day'=>$day]);
                if($goodsCount == null){
                    $goodsCount = new GoodsDayCount();
                    $goodsCount->day = $day;
                    $goodsCount->count = 0;
                    $goodsCount->save();
                }
                //字符串补全
                $model->sn = date('Ymd').sprintf("%04d",$goodsCount->count+1);

                $model->save();
                $introModel->goods_id = $model->id;
                $introModel->save();
                $goodsCount->count++;
                $goodsCount->save();

                //提示
                \Yii::$app->session->setFlash('success','商品添加成功,请添加商品相册');
                //跳转
                return $this->redirect(['goods/gallery','id'=>$model->id]);
            }
        }
        return $this->render('add',['model'=>$model,'introModel'=>$introModel]);
    }

    /*
     * 修改商品信息
     */
    public function actionEdit($id){
        $model = Goods::findOne(['id'=>$id]);
        $introModel = $model->goodsIntro;
        if($model->load(\Yii::$app->request->post()) && $introModel->load(\Yii::$app->request->post())) {
            if ($model->validate() && $introModel->validate()) {
                $model->save();$introModel->save();
                \Yii::$app->session->setFlash('success','商品修改成功');
                return $this->redirect(['goods/index']);
            }
        }
        return $this->render('add',['model'=>$model,'introModel'=>$introModel]);
    }

    //删除
    public function actionDel($id){
        //商品
        $good=Goods::findOne(["id"=>$id]);
        $good->status=0;
        $good->save();
        //提示
        \Yii::$app->session->setFlash("success","删除成功！！");

        //跳转
        return $this->redirect(["goods/index"]);
    }

    /*
     * 商品相册
     */
    public function actionGallery($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('gallery',['goods'=>$goods]);
    }

    /*
    * AJAX删除图片
    */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }
    }

    //预览商品信息
    public function actionView($id)
    {
        $model = Goods::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('view',['model'=>$model]);
    }

    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "http://admin.yii2shop.com",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ],
            ],
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                //END CLOSURE BY TIME
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
                    $goods_id = \Yii::$app->request->post('goods_id');
                    if($goods_id){
                        $model = new GoodsGallery();
                        $model->goods_id = $goods_id;
                        $model->path = $action->getWebUrl();
                        $model->save();
                        $action->output['fileUrl'] = $model->path;
                        $action->output['id'] = $model->id;
                    }else{
                        $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
                    }
                },
            ],
        ];
    }
}