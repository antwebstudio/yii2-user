<?php

namespace ant\user\migrations\db;

use ant\db\Migration;

/**
 * Class M190227032858_alter_user_profile
 */
class M190227032858_alter_user_profile extends Migration
{
	protected $tableName = '{{%user_profile}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'nationality_id', $this->integer()->unsigned()->null()->defaultValue(null));
		$this->addColumn($this->tableName, 'title', $this->string(10)->null()->defaultValue(null));
		
		$this->addForeignKeyTo('{{%address_country}}', 'nationality_id', self::FK_TYPE_CASCADE, self::FK_TYPE_SET_NULL);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKeyTo('{{%address_country}}', 'nationality_id');
	
        $this->dropColumn($this->tableName, 'nationality_id');
        $this->dropColumn($this->tableName, 'title');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190227032858_alter_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
