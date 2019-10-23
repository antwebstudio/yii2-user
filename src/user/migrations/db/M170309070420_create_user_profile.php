<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M170309070420_create_user_profile extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned(),
            'firstname' => $this->string(255),
            'lastname' => $this->string(255),
            'company' => $this->string(255),
            'contact' => $this->string(255),
            'email' => $this->string(),
            'avatar_path' => $this->string(255),
            'avatar_base_url' => $this->string(255),
            'gender' => $this->smallinteger(1)->defaultValue(0),
            'address_id' => $this->integer()->unsigned(),
            'main_profile' => $this->smallinteger(1)->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $tableOptions);
        $this->addForeignKey('fk_user_profile_user_id', '{{%user_profile}}', 'user_id', '{{%user}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_user_profile_address_id', '{{%user_profile}}', 'address_id', '{{%address}}', 'id', null, null);
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_profile_address_id', '{{%user_profile}}');
        $this->dropForeignKey('fk_user_profile_user_id', '{{%user_profile}}');
        $this->dropTable('{{%user_profile}}');
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
