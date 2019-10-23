<?php

namespace ant\contact\migrations\db;

use yii\db\Migration;

/**
 * Class M190226103301_alter_contact
 */
class M190226103301_alter_contact extends Migration
{
	protected $tableName = '{{%contact}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->renameColumn($this->tableName, 'address', 'address_string'); // Because Contact have a "address" relation
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->renameColumn($this->tableName, 'address_string', 'address');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190226103301_alter_contact cannot be reverted.\n";

        return false;
    }
    */
}
