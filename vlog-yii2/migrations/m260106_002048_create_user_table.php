<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m260106_002048_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),

            'username' => $this->string(64)->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),

            // просте керування доступом без RBAC
            'is_admin' => $this->boolean()->notNull()->defaultValue(0),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');


        $time = time();
        $passwordHash = Yii::$app->security->generatePasswordHash('admin');
        $authKey = Yii::$app->security->generateRandomString(32);

        $this->insert('{{%user}}', [
            'username' => 'admin',
            'password_hash' => $passwordHash,
            'auth_key' => $authKey,
            'is_admin' => 1,
            'created_at' => $time,
            'updated_at' => $time,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
