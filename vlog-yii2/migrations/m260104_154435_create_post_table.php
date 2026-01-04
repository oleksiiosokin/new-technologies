<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m260104_154435_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),

            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text()->notNull(),

            'image_path' => $this->string(255)->null(),

            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'published_at' => $this->integer()->null(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_post_category_id', '{{%post}}', 'category_id');
        $this->createIndex('idx_post_status', '{{%post}}', 'status');
        $this->createIndex('idx_post_published_at', '{{%post}}', 'published_at');

        $this->addForeignKey(
            'fk_post_category',
            '{{%post}}',
            'category_id',
            '{{%category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_post_category', '{{%post}}');
        $this->dropTable('{{%post}}');
    }
}
