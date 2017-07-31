<?php
namespace frontend\models;


use yii\base\Model;

class MemberForm extends Model{
    public $username;
    public $password;
    public $rememberMe = false;
    //验证码
    public $code;


    public function rules()
    {
        return [
            [['username','password'],'required',"message"=>"{attribute}错误1111"],
            ['rememberMe', 'boolean'],
            [['code'], 'captcha','captchaAction'=>'member/captcha',"message"=>"{attribute}错误1111" ]


        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码：'
        ];
    }
    public function login()
    {
        //1.1 通过用户名查找用户
        $model = Member::findOne(['username'=>$this->username]);
//        var_dump($model);exit;
        if($model){
            if(\Yii::$app->security->validatePassword($this->password,$model->password_hash)){
                //密码正确.可以登录
                //2 登录(保存用户信息到session)
                \Yii::$app->user->login($model,$this->rememberMe ? 3600 * 24 * 30 : 0);
                return true;
            }else{
                //密码错误.提示错误信息
                $this->addError('password','密码错误');
            }
        }else{
            //用户不存在,提示 用户不存在 错误信息
            $this->addError('username','用户名不存在');
        }
        return false;
    }

}