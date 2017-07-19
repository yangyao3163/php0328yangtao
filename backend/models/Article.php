<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $article_category_id
 * @property integer $sort
 * @property integer $status
 * @property integer $create_time
 */
class Article extends \yii\db\ActiveRecord
{
    //建立和分类模型（ArticleCategory）的关系    1对1
    //先定义get方法
    public function getArticleCategory()
    {
        //hasOne表示1对1  参数1 表示对应模型的完整类名
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);//hasOne 返回一个对象
    }
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
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['article_category_id', 'sort', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
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
            'article_category_id' => '文章分类ID',
            'sort' => '排序',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }

    //获取商品分类选项
    public static function getAuthorOptions()
    {
        return ArrayHelper::map(ArticleCategory::find()->all(),'id','name');
    }
}
