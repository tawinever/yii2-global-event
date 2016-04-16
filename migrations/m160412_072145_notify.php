<?php

use yii\db\Migration;

class m160412_072145_notify extends Migration
{
    public function up()
    {
        $this->createTable('{{%notify}}',[
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'event_code' => $this->string()->notNull(),
            'sender_id' => $this->integer()->notNull(),
            'recipient_id' => $this->string()->notNull(),
            'title' => $this->text()->notNull(),
            'fulltext' => $this->text()->notNull(),
            'notifications' => $this->string()->notNull(),
        ]);

        $this->createIndex('notify_indexes', '{{%notify}}', ['sender_id'], false);
        $this->addForeignKey('sender_fk', '{{%notify}}', 'sender_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%notify}}');
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
