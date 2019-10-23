<?php

namespace ant\collaborator\migrations\db;

use yii\db\Migration;

class M181007054704_alter_collaborator_group extends Migration
{
    protected $tableName = '{{%collaborator_group}}';

    public function safeUp()
    {
        $this->addColumn($this->tableName, 'model_class_id', $this->integer()->unsigned()->notNull()->after('id'));

    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'model_class_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M181007054704_alter_collaborator_group cannot be reverted.\n";

        return false;
    }
    */
}
