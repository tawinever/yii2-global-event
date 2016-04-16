<?php

use yii\db\Migration;

class m160408_113843_user extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'password' => $this->string()->notNull(),
            'auth_key' => $this->string()->notNull(),
            'role' => $this->smallInteger()->defaultExpression('1')->notNull(),
        ]);
        $this->createIndex('uniq_cols', '{{%user}}', ['username','email'], true);

        $this->insert('{{%user}}',[
            'username' => 'admin',
            'email' => 'rauanktl@ya.ru',
            'password' => 'admin',
            'role' => '2',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
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
