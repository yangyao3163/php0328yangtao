<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $full_address
 * @property integer $tel
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','tel','full_address','province','city','area'],"required","message"=>"{attribute}不能为空" ],
            [['tel'], 'string', 'max' => 11],
            [['name', 'full_address'], 'string', 'max' => 255],
            [['province', 'city', 'area'], 'string', 'max' => 50],
            ['tel','match','pattern'=>'/^\d{11}$/','message'=>'手机号码必须是11位'],
            [[ 'type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '收件人',
            'province' => '省',
            'city' => '市',
            'area' => '区',
            'full_address' => '详细地址',
            'tel' => '电话',
            'type' => '默认地址',
        ];
    }
}
