<?php

namespace ant\contact\migrations\db;

use yii\db\Migration;

/**
 * Class M190227041140_alter_contact
 */
class M190227041140_alter_contact extends Migration
{
	protected $tableName = '{{%contact}}';
	
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'fax_number', $this->string()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'fax_number');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190227041140_alter_contact cannot be reverted.\n";

        return false;
    }
    */
}
