<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M180531113238_alter_user_profile extends Migration
{
    public function safeUp()
    {
		$this->addColumn('{{%user_profile}}', 'data', $this->text()->defaultValue(NULL));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'data');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180410113238_alter_user cannot be reverted.\n";

        return false;
    }
    */
}
