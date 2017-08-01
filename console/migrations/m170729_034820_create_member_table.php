<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member`.
 */
class m170729_034820_create_member_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('member', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(255)->comment('用户名'),
            'auth_key'=>$this->string(50)->comment('唯一'),
            'password_hash'=>$this->string(255)->comment('hash密码'),
            'email'=>$this->string(255)->comment('邮件'),
            'tel'=>$this->string(50)->comment('电话'),
            'last_login_time'=>$this->integer()->comment('最后登陆时间'),
            'last_login_ip'=>$this->integer()->comment('最后登陆IP'),
            'status'=>$this->smallInteger(2)->comment('状态'),
            'created_at'=>$this->integer()->comment('添加时间'),
            'updated_at'=>$this->integer()->comment('修改时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('member');
    }
}
