<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use backend\filters\RbacFilter;

class RbacController extends \yii\web\Controller
{
    //权限管理

    //添加权限
    public function actionAddPermission(){
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post())&&$model->validate()){
            $authManager = \Yii::$app->authManager;
            //创建权限
            $permission = $authManager->createPermission($model->name);
            $permission->description = $model->description;
            //保存
            $authManager->add($permission);
            //提示。跳转
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['permission-index']);
        }
       return $this->render('add-permission',['model'=>$model]);
    }

    //权限列表
    public function actionPermissionIndex(){
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getPermissions();
        return $this->render('permission-index',['models'=>$models]);
    }

    //权限修改
    public function actionEditPermission($name){
        //检测权限是否存在
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }
        $model = new PermissionForm();
        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post())&&$model->validate()){
                //将数据赋值给权限
                $permission->name = $model->name;
                $permission->description = $model->description;
                //修改权限
                $authManage->update($name,$permission);
                //提示。跳转
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['permission-index']);
            }
        }else{
            //回显数据
            $model->name = $permission->name;
            $model->description = $permission->description;
        }
        return $this->render('add-permission',['model'=>$model]);
    }

    //权限删除
    public function actionDelPermission($name){
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }else{
            $authManage->remove($permission);
            //提示。跳转
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['permission-index']);
        }

    }

    //角色管理

    //添加角色
    public function actionAddRole()
    {
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //创建
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);
            $role->description = $model->description;
            //保存角色
            $authManager->add($role);
            //给角色加权限
            if(is_array($model->permissions)){
                foreach ($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission) $authManager->addChild($role,$permission);
                }
            }
            //提示。跳转
            \Yii::$app->session->setFlash('success','角色添加成功');
            return $this->redirect(['role-index']);
        }
        return $this->render('add-role',['model'=>$model]);
    }

    //修改角色
    public function actionEditRole($name){
        $authManger=\Yii::$app->authManager;
        $role=$authManger->getRole($name);
        if(!$role==null){
            $model=new RoleForm();
            if(\Yii::$app->request->post()){
                if($model->load(\Yii::$app->request->post()) && $model->validate()){
                    //全部取消关联
                    $authManger->removeChildren($role);
                    if(is_array($model->permissions)){
                        foreach($model->permissions as $permissionName){
                            $permission=$authManger->getPermission($permissionName);
                            if($permission)$authManger->addChild($role,$permission);
                        }
                    }
                    $role->description=$model->description;
                    //更新权限
                    $authManger->update($name,$role);
                    //提示
                    \Yii::$app->session->setFlash('success','修改用户成功');
                    //到INDEX页面
                    return $this->redirect(['role-index']);
                }
            }else{//不是POST提交就回显数据
                $permission=$authManger->getPermissionsByRole($name);
                $model->name=$role->name;
                $model->description=$role->description;
                $model->permissions=ArrayHelper::map($permission,'name','name');
            }
            return $this->render('add-role',['model'=>$model]);
        }else{
            throw new NotFoundHttpException('没有该用户');
        }
    }

    //角色删除
    public function actionDelRole($name){
        $authManage = \Yii::$app->authManager;
        $role = $authManage->getRole($name);
        if($role == null){
            throw new NotFoundHttpException('角色不存在');
        }else{
            $authManage->remove($role);
            //提示。跳转
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['role-index']);
        }

    }

    //角色列表
    public function actionRoleIndex()
    {
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getRoles();
        return $this->render('role-index',['models'=>$models]);
    }

    //行为
    public function behaviors()
    {
        return [
            'rbac' => [
                'class' => RbacFilter::className(),
            ]
        ];

    }

}
