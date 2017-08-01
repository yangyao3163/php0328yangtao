<?php

use yii\db\Migration;

class m170731_151711_add_column_to_address extends Migration
{
    public function up()
    {
        $this->addColumn('address', 'type', $this->string(10));
    }

    public function down()
    {
        echo "m170731_151711_add_column_to_address cannot be reverted.\n";

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
