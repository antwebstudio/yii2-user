<?php

namespace ant\user\migrations\db;

use yii\db\Migration;
use ant\user\models\User;

class M170427055856_user_invite extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_invite}}', [
            'id' => $this->primaryKey()->unsigned(),
            'email' => $this->string()->notNull()->unique(),
			'user_id' => $this->integer(11)->unsigned()->defaultValue(NULL),
            'status' => $this->smallInteger()->notNull()->defaultValue(User::STATUS_NOT_ACTIVATED),
            'role' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
            'created_by' => $this->integer(11)->unsigned()->defaultValue(NULL),
            'updated_by' => $this->integer(11)->unsigned()->defaultValue(NULL),
            'token_id' => $this->integer()->unsigned(),
            'data' => $this->text()->defaultValue(NULL),
        ], $tableOptions);

        $this->addForeignKey('fk_user_invite_token_id', '{{%user_invite}}', 'token_id', '{{%token}}', 'id', 'set null', 'cascade');

    }

    public function down()
    {
        $this->dropForeignKey('fk_user_invite_token_id', '{{%user_invite}}');
        $this->dropTable('{{%user_invite}}');
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
