<?php

namespace ant\address\migrations\db;

use yii\db\Migration;

class M170309070341_create_address extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%address}}', [
            'id' => $this->primaryKey()->unsigned(),
            'firstname' => $this->string(32)->null()->defaultValue(null),
            'lastname' => $this->string(32)->null()->defaultValue(null),
            'company' => $this->string(64)->null()->defaultValue(null),
            'venue' => $this->text()->null()->defaultValue(null),
            'address_1' => $this->string()->null()->defaultValue(null),
            'address_2' => $this->string()->null()->defaultValue(null),
            'city' => $this->string(128)->null()->defaultValue(null),
            'postcode' => $this->string(10)->null()->defaultValue(null),
            'country_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'zone_id' => $this->integer()->null()->defaultValue(null),
            'custom_state' => $this->string(128)->null()->defaultValue(null),
            'readonly' => $this->smallinteger(1)->notNull()->defaultValue(0),
            'del' => $this->smallinteger(1)->notNull()->defaultValue(0),
            'latitude' => $this->decimal(10, 8),
            'longitude' => $this->decimal(11, 8),
            'created_at' => $this->timestamp()->null()->defaultValue(null),
            'updated_at' => $this->timestamp()->null()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey('fk_address_country_id', '{{%address}}', 'country_id', '{{%address_country}}', 'id', null, null);
        $this->addForeignKey('fk_address_zone_id', '{{%address}}', 'zone_id', '{{%address_zone}}', 'id', null, null);
    }

    public function down()
    {
        $this->dropForeignKey('fk_address_country_id', '{{%address}}');
        $this->dropForeignKey('fk_address_zone_id', '{{%address}}');
        $this->dropTable('{{%address}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
