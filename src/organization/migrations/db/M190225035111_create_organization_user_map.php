<?php

namespace ant\organization\migrations\db;

use ant\db\Migration;

/**
 * Class M190225035111_create_organization_user_map
 */
class M190225035111_create_organization_user_map extends Migration
{
	protected $tableName = '{{%organization_user_map}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'organization_id' => $this->integer()->unsigned()->notNull(),
            'position_title' => $this->string()->null()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $this->getTableOptions());
		
		$this->addForeignKeyTo('{{%user}}', 'user_id', self::FK_TYPE_CASCADE, self::FK_TYPE_CASCADE);
		$this->addForeignKeyTo('{{%organization}}', 'organization_id', self::FK_TYPE_CASCADE, self::FK_TYPE_CASCADE);

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
        echo "M190225035111_create_organization_user_map cannot be reverted.\n";

        return false;
    }
    */
}
