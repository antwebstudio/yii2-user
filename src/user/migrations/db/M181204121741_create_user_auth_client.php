<?php

namespace ant\user\migrations\db;

use ant\db\Migration;

/**
 * Class M181204121741_create_user_auth_client
 */
class M181204121741_create_user_auth_client extends Migration
{
    protected $tableName = '{{%user_auth_client}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->string()->notNull(),
            'data' => $this->text()->null()->defaultValue(null),
        ]);

        $this->addForeignKeyTo('{{%user}}', 'user_id', self::FK_TYPE_CASCADE, self::FK_TYPE_CASCADE);
    }

    /**
     * {@inheritdoc}
     */
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
        echo "M181204121741_create_user_auth_client cannot be reverted.\n";

        return false;
    }
    */
}
