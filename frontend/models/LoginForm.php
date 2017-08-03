<?php

namespace frontend\models;

use yii\base\Model;

class LoginForm extends Model{

    public $username;//用户名
    public $password;//密码
    public $code;//验证码
    public $rememberMe = false;


    public function rules()
    {
        return [
            [['username','password','code'],'required'],
            ['rememberMe', 'boolean'],
            ['code','captcha','captchaAction'=>'member/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
        ];
    }
    //验证登录
    public function login(){
        $member=Member::findOne(["username"=>$this->username]);
        if ($member){
            if(\Yii::$app->security->validatePassword($this->password,$member->password_hash)){
                \Yii::$app->user->login($member,$this->rememberMe ? 3600*5*25 : 0);
                return true;
            }else{
                $this->addError("password","密码错误");
            }
        }else{
            $this->addError("username","用户名不存在");
        }
        return false;
    }
}