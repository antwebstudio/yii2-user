<?php

namespace ant\user\migrations\db;

use ant\db\Migration;

class M180101084638_create_user_config extends Migration
{
	protected $tableName = '{{%user_config}}';
	
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(11)->unsigned()->defaultValue(NULL),
            'config_name' => $this->string(100)->notNull(),
            'value' => $this->text()->defaultValue(Null),
        ],   $this->getTableOptions());

        $this->addForeignKeyTo('{{%user}}', 'user_id', self::FK_TYPE_CASCADE, self::FK_TYPE_RESTRICT);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {

    }
    */
}
