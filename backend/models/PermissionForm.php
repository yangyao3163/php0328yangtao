<?php

namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model
{
    //权限名
    public $name;
    //权限描述
    public $description;

    const SCENARIO_ADD = 'add';

    public function rules()
    {
        return [
            [['name','description'],'required',"message"=>"{attribute}不能为空"],
            //自定义规则名称不能重复
            ['name','validateName','on'=>self::SCENARIO_ADD]

        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'description'=>'描述'
        ];
    }

    public function validateName(){
        $authManage = \Yii::$app->authManager;
        if($authManage->getPermission($this->name)){
            $this->addError('name','权限已经存在');
        };
    }
}