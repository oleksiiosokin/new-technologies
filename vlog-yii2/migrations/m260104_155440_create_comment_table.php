<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m260104_155440_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp(): void
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),

            'post_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->null(),

            'author_name' => $this->string(80)->notNull(),
            'author_email' => $this->string(120)->null(),

            'content' => $this->text()->notNull(),

            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_comment_post_id', '{{%comment}}', 'post_id');
        $this->createIndex('idx_comment_parent_id', '{{%comment}}', 'parent_id');
        $this->createIndex('idx_comment_status', '{{%comment}}', 'status');

        $this->addForeignKey(
            'fk_comment_post',
            '{{%comment}}',
            'post_id',
            '{{%post}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_comment_parent',
            '{{%comment}}',
            'parent_id',
            '{{%comment}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_comment_parent', '{{%comment}}');
        $this->dropForeignKey('fk_comment_post', '{{%comment}}');

        $this->dropTable('{{%comment}}');
    }
}
