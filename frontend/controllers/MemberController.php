<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\MemberForm;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;

class MemberController extends \yii\web\Controller
{
    //不加载导航栏
    public $layout = false;
    //关闭csrf验证
    public $enableCsrfValidation = false;
    //注册
    public function actionRegister(){
//        $model->scenario = Member::SCENARIO_REGISTER;

        //实例化
        $model = new Member();
        //调用视图
        return $this->render('register',['model'=>$model]);
    }

    //ajax验证
    public function actionAjaxRegister()
    {
        $model = new Member();
        //使用注册场景
        $model->scenario = Member::SCENARIO_REGISTER;
        if($model->load(\Yii::$app->request->post()) && $model->validate() ){
            //随机生成密钥
            $model->auth_key = \Yii::$app->security->generateRandomString();
            //添加时间
            $model->created_at = time();
            //状态默认为1=>正常，0=>删除
            $model->status = 1;
            //hash密码
            if($model->password) $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
            //保存数据
            $model->save(false);
            //提示保存成功
            return Json::encode(['status'=>true,'msg'=>'注册成功']);
        }else{
            //验证失败，提示错误信息
            return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
        }
    }
    //用户登录
    public function actionLogin(){
        $model=new LoginForm();
        return $this->render("login",["model"=>$model]);
    }
    //登录ajax
    public function actionAjaxLogin()
    {
        $model=new LoginForm();
        if ($model->load(\Yii::$app->request->post())&& $model->validate()){
            if($model->login()){
                $member=Member::findOne(["username"=>$model->username]);
//                echo $model->username;exit;
                $member->last_login_time=time();
                $member->last_login_ip=ip2long(\Yii::$app->request->userIP);
                $member->save();
                $member->saveCart();
                return Json::encode(['status'=>true,'msg'=>'登陆成功']);
            }
        }
        return Json::encode(["status"=>false,"msg"=>$model->getErrors()]);
    }


//检测用户是否登录
    public function actionUser()
    {
        //可以通过 Yii::$app->user 获得一个 User实例，
        $user = \Yii::$app->user;

        // 当前用户的身份实例。未认证用户则为 Null 。
        $identity = \Yii::$app->user->identity;
        var_dump($identity);

        // 当前用户的ID。 未认证用户则为 Null 。
        $id = \Yii::$app->user->id;
        var_dump($id);
        // 判断当前用户是否是游客（未认证的）
        $isGuest = \Yii::$app->user->isGuest;
        var_dump($isGuest);
    }

    //收获地址管理
    public function actionAddress(){
        $model = new Address();
        $address = Address::find()->all();
//        var_dump($model);exit;
        return $this->render("address",['model'=>$model,'address'=>$address]);
    }

    //ajax保存收货地址
    public function actionAjaxAddress(){
        $model = new Address();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //保存数据
            $model->save(false);
            //提示
            return Json::encode(['status'=>true,'msg'=>'保存成功']);
        }else{
            return Json::encode(["status"=>false,"msg"=>$model->getErrors()]);
        }
    }
    //修改地址
    public function actionEditAddress(){
        $model=Address::findOne(["id"=>\Yii::$app->request->get("id")]);
        if (!$model){
            throw new NotFoundHttpException("没有此地址");
        }
        return Json::encode(["name"=>$model->name,"province"=>$model->province,'area'=>$model->area,"full_address"=>$model->full_address, "city"=>$model->city,"type"=>$model->type,"tel"=>$model->tel,"id"=>$model->id]);
    }

    public function actionEdit($id){
        $model=Address::findOne(["id"=>$id]);
        if (!$model){
            throw new NotFoundHttpException("没有此地址");
        }
        $model->type=1;
        $model->save();
        return $this->redirect("address");
    }

    //删除地址
    public function actionDelAddress($id){
        $model=Address::findOne(["id"=>$id]);
        if (!$model){
            throw new NotFoundHttpException("没有此地址");
        }
        $model->delete();
    }


    //首页
    public function actionIndex(){

        $model = Goods::find()->limit(5)->all();

        return $this->render('index',['model'=>$model]);

    }

    //短信功能
    public function actionTestSms(){
        //SMS_80130111 农夫小灶
        //LTAI7xmAkWvZuPA4
        //kCUg8JWnDXssCwubGdnMGIo0aeQNSu
        //获取用户输入的手机号
        $tel = $_POST['tel'];
        //随机生成验证码
        $code = rand(1000,9999);
        $res = \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$code])->send();
        //将短信验证码保存到session
        \Yii::$app->session->set('code_'.$tel,$code);
        if($res){
            return Json::encode(['status'=>true,'msg'=>'验证码发送成功']);
        }else{
            return Json::encode(['status'=>false,'msg'=>'验证码发送失败']);
        }

    }








    public function actions() {
        return [
            'captcha' =>  [
                'class' => 'yii\captcha\CaptchaAction',
                'height' => 50,
                'width' => 80,
                'minLength' => 4,
                'maxLength' => 4
            ],
        ];
    }

}
