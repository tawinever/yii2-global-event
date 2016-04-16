<?php

use yii\db\Migration;

class m160415_111609_message extends Migration
{
    public function up()
    {
        $this->createTable('{{%message}}',[
            'id' => $this->primaryKey(),
            'from' => $this->integer()->notNull(),
            'to' => $this->integer()->notNull(),
            'title' => $this->text(),
            'fulltext'=> $this->text(),
            'status'=>$this->smallInteger()->defaultExpression('1'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),

        ]);
        $this->addForeignKey('from_fk', '{{%message}}', 'from', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('to_fk', '{{%message}}', 'to', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable("{{%message}}");
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
