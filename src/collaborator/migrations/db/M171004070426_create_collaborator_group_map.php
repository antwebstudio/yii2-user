<?php

namespace ant\collaborator\migrations\db;

use yii\db\Migration;

class M171004070426_create_collaborator_group_map extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%collaborator_group_map}}', [
            'id' => $this->primaryKey()->unsigned(),
            'collaborator_group_id' => $this->integer()->unsigned(),
            'user_id' => $this->integer()->unsigned()
        ], $tableOptions);

        $this->addForeignKey('fk_collaborator_group_map_collaborator_group_id', '{{%collaborator_group_map}}', 'collaborator_group_id', '{{%collaborator_group}}', 'id', 'cascade', 'cascade');
        
        $this->addForeignKey('fk_collaborator_group_map_user_id_user', '{{%collaborator_group_map}}', 'user_id',
         '{{%user}}', 'id', 'cascade', 'cascade');

    }

    public function down()
    {
        $this->dropForeignKey('fk_collaborator_group_map_collaborator_group_id', '{{%collaborator_group_map}}');   
        $this->dropForeignKey('fk_collaborator_group_map_user_id_user', '{{%collaborator_group_map}}');    
        $this->dropTable('{{%collaborator_group_map}}');
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
