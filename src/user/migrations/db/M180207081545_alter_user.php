<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M180207081545_alter_user extends Migration
{
	protected $tableName = '{{%user_profile}}';
	
    public function safeUp()
    {
		$this->addColumn($this->tableName, 'company_website_url', $this->string(255)->defaultValue(Null));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'company_website_url');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M170613081545_alter_user cannot be reverted.\n";

        return false;
    }
    */
}
