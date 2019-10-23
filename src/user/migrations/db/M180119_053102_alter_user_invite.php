<?php

namespace ant\user\migrations\db;

use yii\db\Migration;

class M180119_053102_alter_user_invite extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user_invite}}', 'type', $this->string(255)->defaultValue(Null));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user_invite}}', 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M170629072255_alter_event_organizer cannot be reverted.\n";

        return false;
    }
    */
}
