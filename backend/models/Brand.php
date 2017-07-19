<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
//    public $imgFile;//保存图片上传的对象

    //隐藏删除的按钮
    public static function getStatusOptions($hidden_del = true){
        $options =  [
          -1 =>'删除',0=>'隐藏',1=>'显示'
        ];
        if($hidden_del){
            unset($options['-1']);
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [ ["name","intro","sort","status"],"required","message"=>"{attribute}必填" ],
            [['name'], 'string', 'max' => 50],
            [['intro'], 'string'],
//            ['imgFile','file','extensions'=>['jpg','png','gif']],
            [['sort', 'status'], 'integer'],
            [['status'], 'string', 'max' => 255],
            [['logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'sort' => '排序',
            'status' => '状态',
            'logo' => 'LOGO图片',
        ];
    }
}
