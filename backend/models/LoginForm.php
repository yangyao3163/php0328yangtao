<?php
namespace backend\models;


class LoginForm extends \yii\base\Model{
    public $username;
    public $password_hash;
    public $rememberMe = false;

    public function rules()
    {
        return [
            [['username','password_hash'],'required'],
            ['rememberMe', 'boolean']

        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码'

        ];
    }
    public function login()
    {
        //1.1 通过用户名查找用户
        $user = User::findOne(['username'=>$this->username]);
        if($user){
            if(\Yii::$app->security->validatePassword($this->password_hash,$user->password_hash)){
                //密码正确.可以登录
                //2 登录(保存用户信息到session)
                \Yii::$app->user->login($user,$this->rememberMe ? 3600 * 24 * 30 : 0);
                return true;
            }else{
                //密码错误.提示错误信息
                $this->addError('password_hash','密码错误');
            }
        }else{
            //用户不存在,提示 用户不存在 错误信息
            $this->addError('username','用户名不存在');
        }
        return false;
    }



}