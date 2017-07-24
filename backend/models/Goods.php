<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    //是否上架
    public static $sale_options = [1=>'上架',0=>'下架'];
    public static $status_options = [1=>'正常',0=>'删除'];

    //商品的状态
    public static function getStatusOptions($hide_del=true){
        $options=[
            '0'=>"回收站",
            '1'=>"正常"
        ];
        if($hide_del){
            unset( $options['0']);
        }
        return $options;
    }
    /*
     * 品牌选项
     */
    public static function getBrandOptions(){
        return ArrayHelper::map(Brand::find()->where(['!=','status',-1])->asArray()->all(),'id','name');
    }

    //创建时间，点击数
    public function beforeSave($insert)
    {
        if($insert){
            $this->create_time = time();
            $this->view_times = 0;
        }
        return parent::beforeSave($insert);
    }

    /*
    * 获取商品详情
    */
    public function getGoodsIntro()
    {
        return $this->hasOne(GoodsIntro::className(),['goods_id'=>'id']);
    }

    //获取图片轮播数据
    public function getPics()
    {
        $images = [];
        foreach ($this->galleries as $img){
            $images[] = Html::img($img->path);
        }
        return $images;
    }

    /*
     * 商品和相册关系 1对多
     */
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["name",'sort','status','logo','goods_category_id','market_price','shop_price','stock','is_on_sale'],"required","message"=>"{attribute}不能为空"],
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time', 'view_times'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn'], 'string', 'max' => 20],
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
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'LOGO',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在售',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }
}
