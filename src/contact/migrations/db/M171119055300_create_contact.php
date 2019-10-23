<?php

namespace ant\contact\migrations\db;

use ant\db\Migration;

class M171119055300_create_contact extends Migration
{
    protected $tableName = '{{%contact}}';
	
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'firstname' => $this->string()->defaultValue(NULL),
            'lastname' => $this->string()->defaultValue(NULL),
            'contact_name' => $this->string()->defaultValue(NULL),
            'organization' => $this->string()->defaultValue(NULL),
            'contact_number' => $this->string()->defaultValue(NULL),
            'email' => $this->string()->defaultValue(NULL),
            'address_id' => $this->integer(11)->unsigned()->defaultValue(NULL),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer(11)->unsigned(),
            'updated_by' => $this->integer(11)->unsigned(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $this->getTableOptions());
		
		$this->addForeignKeyTo('{{%address}}', 'address_id', self::FK_TYPE_CASCADE, self::FK_TYPE_RESTRICT);


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
        echo "M171119055300_create_contact cannot be reverted.\n";

        return false;
    }
    */
}
