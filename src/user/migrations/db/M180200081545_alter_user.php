<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M180200081545_alter_user extends Migration
{
	protected $tableName = '{{%user}}';
	
    public function safeUp()
    {
		$this->alterColumn($this->tableName, 'email', $this->string(45)->defaultValue(NULL)->unique());
    }
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'email', $this->string(45)->notNull()->unique());
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
