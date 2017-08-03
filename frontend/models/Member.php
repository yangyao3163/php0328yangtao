<?php

namespace frontend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $tel
 * @property integer $last_login_time
 * @property integer $last_login_ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    //注册场景
    const SCENARIO_REGISTER = 'register';
    //密码
    public $password;
    //确认密码
    public $rePassword;
    //图像验证码
    public $code;
    //短信验证码
    public $smsCode;
    public $rememberMe = false;//自动登录

    public function saveCart(){
        //用户登录成功
        //1.获取cookie中的购物车数据，
        $cookies = \Yii::$app->request->cookies;
        $carts =unserialize($cookies->get('cart'));
        //2.循环遍历购物车数据
        foreach($carts as $goods_id=>$amount){
            //(使用goods_id作为查询条件，member_id)
            $cart = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$this->id]);
            if($cart){
                //2.1如果数据表已经有这个商品,就合并cookie中的数量
                $cart->amount += $amount;
                $cart->save();
            }else{
                //2.2如果数据表没有这个商品,就添加这个商品到购物车表
                $cart = new Cart();
                $cart->goods_id = $goods_id;//商品ID
                $cart->amount = $amount;//商品数量
                $cart->member_id = $this->id;//对应的用户id
                $cart->save();
            }
        }
        //同步完后，清空cookie购物车
        \Yii::$app->response->cookies->remove('cart');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rePassword', 'compare', 'compareAttribute'=>'password', 'message'=>'两次输入的密码不相同'],
            [['username'],"required","message"=>"{attribute}不能为空" ],
            [['code','smsCode'],'required','on'=>self::SCENARIO_REGISTER ],
            [['password','rePassword'],"required",'on'=>self::SCENARIO_REGISTER,"message"=>"{attribute}不能为空" ],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'email'], 'string', 'max' => 255],
            [['auth_key', 'tel'], 'string', 'max' => 50],
            [['code'], 'captcha','captchaAction'=>'member/captcha',"message"=>"{attribute}错误" ,'on'=>self::SCENARIO_REGISTER],
            ['rememberMe', 'boolean'],//自动登录
            ['smsCode',"validateCode",'on'=>self::SCENARIO_REGISTER],
        ];
    }
    public function validateCode(){
        //验证
        $code2 = \Yii::$app->session->get('code_'.$this->tel);
        if($code2 != $this->smsCode){
            $this->addError("smscode","手机验证码错误");
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => '唯一',
            'password_hash' => 'hash密码',
            'email' => '邮件',
            'tel' => '电话',
            'last_login_time' => '最后登陆时间',
            'last_login_ip' => '最后登陆IP',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'code'=>'验证码',
            'smsCode' => '手机验证码',
            'password'=>'密码',
            'rePassword'=>'确认密码',
        ];
    }


    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(["id"=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key=$authKey;
    }
}
