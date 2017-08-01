<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170731_075622_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(255)->comment('收件人'),
            'province'=>$this->string(50)->comment('省'),
            'city'=>$this->string(50)->comment('市'),
            'area'=>$this->string(50)->comment('区'),
            'full_address'=>$this->string(255)->comment('详细地址'),
            'tel'=>$this->integer(11)->comment('电话'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
