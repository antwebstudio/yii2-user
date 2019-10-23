<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M180410113238_alter_user extends Migration
{
    public function safeUp()
    {
		$this->addColumn('{{%user}}', 'is_approved', $this->smallInteger(1)->notNull()->defaultValue(0));
		$this->addColumn('{{%user}}', 'approved_at', $this->timestamp()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'is_approved');
        $this->dropColumn('{{%user}}', 'approved_at');
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
