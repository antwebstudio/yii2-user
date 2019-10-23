<?php

namespace ant\organization\migrations\db;

use ant\db\Migration;

/**
 * Class M190225033916_create_organization
 */
class M190225033916_create_organization extends Migration
{
	protected $tableName = '{{%organization}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull(),
            'contact_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
			'founded_year' => $this->smallInteger(5)->null()->defaultValue(null),
			'registration_number' => $this->string(20)->null()->defaultValue(null),
			'website_url' => $this->string()->null()->defaultValue(null),
			'collaborator_group_id' => $this->integer()->unsigned()->null()->defaultValue(null),
			'data' => $this->text()->null()->defaultValue(null),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ], $this->getTableOptions());
		
		$this->addForeignKeyTo('{{%contact}}', 'contact_id', self::FK_TYPE_RESTRICT, self::FK_TYPE_SET_NULL);
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
        echo "M190225033916_create_organization cannot be reverted.\n";

        return false;
    }
    */
}
