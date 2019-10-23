<?php

namespace ant\contact\migrations\db;

use yii\db\Migration;

class M180420071630_alter_contact extends Migration
{
	protected $tableName = '{{%contact}}';
	
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'address', $this->string()->defaultValue(null));
		$this->addColumn($this->tableName, 'ic_passport', $this->string(14)->defaultValue(null));
		$this->addColumn($this->tableName, 'data', $this->text()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'address');
        $this->dropColumn($this->tableName, 'ic_passport');
        $this->dropColumn($this->tableName, 'data');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180420071630_alter_contact cannot be reverted.\n";

        return false;
    }
    */
}
