<?php

namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permissions=[];

    const SCENARIO_ADD = 'add';



    public function rules()
    {
        return [
            [['name','description'],'required',"message"=>"{attribute}不能为空"],
            ['permissions','safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD]

        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'角色描述',
            'permissions'=>'角色权限'
        ];
    }

    public function validateName(){
        $authManage = \Yii::$app->authManager;
        if($authManage->getRole($this->name)){
            $this->addError('name','角色已经存在');
        };
    }
}