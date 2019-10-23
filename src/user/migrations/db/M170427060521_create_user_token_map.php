<?php

namespace ant\user\migrations\db;

use ant\db\Migration;

class M170427060521_create_user_token_map extends Migration
{
	protected $tableName = '{{%user_token_map}}';
	
    public function up()
    {
        $this->createTable($this->tableName, [
            'user_id' => $this->integer()->unsigned(),
            'token_id' => $this->integer()->unsigned(),
        ], $this->getTableOptions());

        $this->addForeignKey('fk_user_token_map_user_id', '{{%user_token_map}}', 'user_id', '{{%user}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_user_token_map_token_id', '{{%user_token_map}}', 'token_id', '{{%token}}', 'id', 'cascade', 'cascade');
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_token_map_token_id', '{{%user_token_map}}');
        $this->dropForeignKey('fk_user_token_map_user_id', '{{%user_token_map}}');
        $this->dropTable('{{%user_token_map}}');
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
