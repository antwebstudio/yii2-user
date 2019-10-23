<?php

namespace ant\user\migrations\db;

use ant\db\Migration;

/**
 * Class M190219053124_create_user_identity
 */
class M190219053124_create_user_identity extends Migration
{
	protected $tableName = '{{%user_identity}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
			'type' => $this->string(10)->notNull()->defaultValue('ic'),
			'value' => $this->string(20)->notNull(),
        ], $this->getTableOptions());

        $this->addForeignKeyTo('{{%user}}', 'user_id', 'cascade', 'cascade');
		$this->createUniqueIndexFor(['type', 'value']);
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
        echo "M190219053124_create_user_identity cannot be reverted.\n";

        return false;
    }
    */
}
