<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170728_022316_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'menu_name'=>$this->string(50)->comment('菜单名称'),
            'superior_menu'=>$this->string(50)->comment('上级菜单'),
            'menu_url'=>$this->string(255)->comment('菜单地址'),
            'sort'=>$this->integer()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
