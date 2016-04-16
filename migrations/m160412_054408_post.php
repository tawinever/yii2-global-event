<?php

use yii\db\Migration;

class m160412_054408_post extends Migration
{
    /**
     * Generating post table in DB, with 1 demo record
     */
    public function up()
    {
        $this->createTable('{{%post}}',[
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'fulltext' => $this->text()->notNull(),
            'author' => $this->integer(),
            'url' => $this->string()->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('author_fk', '{{%post}}', 'author', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->insert('{{%post}}',[
            'title' => 'First post title',
            'fulltext' => 'It is first post title',
            'author' => '1',
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%post}}');
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
