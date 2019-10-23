<?php

namespace ant\user\migrations\db;

use ant\db\Migration;

/**
 * Class M190716025733_alter_user_profile
 */
class M190716025733_alter_user_profile extends Migration
{
	protected $tableName = '{{%user_profile}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'contact_id', $this->integer()->null()->unsigned()->defaultValue(null));
        $this->renameColumn($this->tableName, 'contact', 'contact_number');

        $this->addForeignKeyTo('{{%contact}}', 'contact_id', self::FK_TYPE_CASCADE, self::FK_TYPE_SET_NULL);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKeyTo('{{%contact}}', 'contact_id');

        $this->renameColumn($this->tableName, 'contact_number', 'contact');
        $this->dropColumn($this->tableName, 'contact_id');
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190716025733_alter_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
