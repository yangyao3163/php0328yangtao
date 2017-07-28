<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $menu_name
 * @property string $superior_menu
 * @property string $menu_url
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    //子类
    public function getChildren(){
        return $this->hasMany(self::className(),['superior_menu'=>'id']);
    }

    //URL
    public static function getUrlOptions(){
        return ArrayHelper::map(\Yii::$app->authManager->getPermissions(),'name','name');
    }

    //分类
    public static function getPidOptions(){
        return ArrayHelper::merge(['menu_name'=>'顶级分类'],ArrayHelper::map(self::find()->where(['superior_menu'=>0])->asArray()->all(),'id','menu_name'));
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_name'], 'required',"message"=>"{attribute}不能为空"],
            [['sort'], 'integer'],
            [['menu_name', 'superior_menu'], 'string', 'max' => 50],
            [['menu_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_name' => '菜单名称',
            'superior_menu' => '上级菜单',
            'menu_url' => '菜单地址',
            'sort' => '排序',
        ];
    }
}
