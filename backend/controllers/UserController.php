<?php

namespace backend\controllers;

use backend\models\ChangePasswordForm;
use backend\models\LoginForm;
use backend\models\User;
use yii\data\Pagination;
use yii\web\Request;
use yii\helpers\ArrayHelper;
use backend\filters\RbacFilter;

class UserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        //分页 总条数 每页显示条数 当前第几页
        $query = User::find()->where(['!=','status','-1']);
        //总条数
        $total = $query->count();
        //每页显示条数
        $perPage = 3;
        //分页工具条
        $pager = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage
        ]);
        //每页显示几条
        $model = $query->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render('index',["model"=>$model,'pager'=>$pager]);
    }

    //添加
    public function actionAdd(){
        //实例化模型
        $model = new User();
        $authManger=\Yii::$app->authManager;
        //判断post提交，加载表单数据
        if($model->load(\Yii::$app->request->post())&& $model->validate()){
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            $model->save();
            //角色
            if(is_array($model->role)){
                foreach($model->role as $roleName){
                    $role=$authManger->createRole($roleName);
                    if($role)$authManger->assign($role,$model->id);
                }
            }
            //提示
            \Yii::$app->session->setFlash("success","品牌添加成功");
            //跳转
            return $this->redirect(["user/index"]);
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }

    //修改
    public function actionEdit($id){
        //实例化模型,根据ID来修改
        $model = User::findOne(['id'=>$id]);//回显用户
        $authManger=\Yii::$app->authManager;
        $role=$authManger->getRolesByUser($model->id);
        $model->role=ArrayHelper::map($role,'name','name');
        //判断post提交，加载表单数据
        if($model->load(\Yii::$app->request->post())&& $model->validate()){
            $authManger->revokeAll($model->id);
            if(is_array($model->role)){
                foreach($model->role as $roleName){
                    $role=$authManger->getRole($roleName);
                    if($role)$authManger->assign($role,$model->id);
                }
            }
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            $model->save();
            //提示
            \Yii::$app->session->setFlash("success","品牌修改成功");
            //跳转
            return $this->redirect(["user/index"]);
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDel($id)
    {
        $model=User::findOne(['id'=>$id]);
        $model->status= -1;
        $model->save(false);
        //提示
        \Yii::$app->session->setFlash("danger","品牌删除成功");
        return $this->redirect(['user/index']);
    }

//登录
    public function actionLogin(){
        $model=new LoginForm();
        if($model->load(\Yii::$app->request->post())&&$model->validate()){
            $model->password_hash;
            if($model->validate() && $model->login()){
                $user=User::findOne(["username"=> $model->username]);
//                var_dump($user);exit;
                $user->last_login_time=time();
                $user->last_login_ip=ip2long(\Yii::$app->request->userIP);
                $user->save(false);
                //登录成功
                \Yii::$app->session->setFlash('success','登录成功');
                return $this->redirect(['user/index']);
            }
        }
        return $this->render("login",["model"=>$model]);
    }
    //退出
    public function actionLogout(){
        \Yii::$app->user->logout();
        //登录成功
        \Yii::$app->session->setFlash('success','退出成功');
        return $this->redirect(['user/index']);
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

    //修改自己密码（登录状态才能使用）
    public function actionChPw(){
        $model=new ChangePasswordForm();
        //表单字段  旧密码 新密码 确认新密码
        if($model->load(\Yii::$app->request->post())&&$model->validate()){
            //得到当前用户的id
            $id = \Yii::$app->user->id;
            if ($id){
                //得到当前用户的信息
                $user=User::findOne(["id"=>$id]);
                if(\Yii::$app->security->validatePassword($model->oldPassword, $user->password_hash)){
                    //保存新密码加密
                    $user->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                    //保存
                    $user->save();
//                    var_dump( $user->password_hash);exit;
                    //旧密码不正确
                    \Yii::$app->session->setFlash("danger","密码修改成功");
                    return $this->redirect(['user/index']);
                }else{
                    //旧密码不正确
                    \Yii::$app->session->setFlash("danger","旧密码输入错误");
                    return $this->redirect(['user/ch-pw']);
                }

            }else{
                \Yii::$app->session->setFlash("danger","请登录");
                return $this->redirect(['user/login']);
            }
        }
        //验证规则  都不能为空  验证旧密码是否正确  新密码不能和旧密码一样  确认新密码和新密码一样
        //表单验证通过 更新新密码
        return $this->render("chpw",["model"=>$model]);
    }


    //行为
    public function behaviors()
    {
        return [
            'rbac' => [
                'class' => RbacFilter::className(),
                'except'=>[
                    'login','logout'
                ],
            ]
        ];

    }
}
