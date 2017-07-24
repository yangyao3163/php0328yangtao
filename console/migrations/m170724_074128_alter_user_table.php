<?php

use yii\db\Migration;

class m170724_074128_alter_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user','last_login_time','integer comment "最后登录时间" ');
        $this->addColumn("user","last_login_ip","integer comment '最后登录id' ");


    }

    public function down()
    {
        echo "m170724_074128_alter_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
