<?php
namespace backend\models;

use yii\base\Model;

class ChangePasswordForm extends Model{
    public $oldPassword;//旧密码
    public $password;//新密码
    public $rePassword;//确认密码

    public function rules()
    {
        return [
            [["oldPassword","password","rePassword"],"required"],
            //检查用户输入的密码是否是一样的
            ['rePassword', 'compare', 'compareAttribute'=>'password', 'message'=>'两次输入的密码不相同'],
            ['password', 'compare', 'compareAttribute'=>'oldPassword','operator'=>'!=', 'message'=>'新密码不能和旧密码相同'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword'=>'旧密码',
            'password'=>'新密码',
            'rePassword'=>'再次密码',
        ];
    }
}