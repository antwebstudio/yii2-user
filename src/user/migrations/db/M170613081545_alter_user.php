<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M170613081545_alter_user extends Migration
{
	protected $tableName = '{{%user}}';
	
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'registered_ip', $this->string(45)->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'registered_ip');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M170613081545_alter_user cannot be reverted.\n";

        return false;
    }
    */
}
