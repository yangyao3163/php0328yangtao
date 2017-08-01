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
    //自动登录
    public $rememberMe = false;

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
            [['password','rePassword'],"required",'on'=>self::SCENARIO_REGISTER,"message"=>"{attribute}不能为空" ],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'email'], 'string', 'max' => 255],
            [['auth_key', 'tel'], 'string', 'max' => 50],
            [['code'], 'captcha','captchaAction'=>'member/captcha',"message"=>"{attribute}错误" ,'on'=>self::SCENARIO_REGISTER],
            ['rememberMe', 'boolean'],//自动登录

        ];
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
        // TODO: Implement findIdentity() method.
        return self::findOne(['id'=>$id]);
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
        return static::findOne(['accessToken'=>$token]);

    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
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
        // TODO: Implement getAuthKey() method.
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
        // TODO: Implement validateAuthKey() method.
        return $this->auth_key === $authKey;

    }

    //生成随机的auth_key，用于cookie登陆
    public function generateAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
        $this->save();
    }
}
