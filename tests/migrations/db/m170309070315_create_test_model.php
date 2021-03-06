<?php

use ant\components\Migration;
use yii\db\Expression;

class m170309070315_create_test_model extends Migration
{
    public function up()
    {
        $this->createTable('{{%test}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull()->default('test'),
			'collaborator_group_id' => $this->integer()->null(),
            'created_at' => $this->timestamp()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->defaultValue(NULL),
        ], $this->getTableOptions());
    }

    public function down()
    {
       $this->dropTable('{{%test}}');
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
