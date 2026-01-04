<?php

use yii\db\Migration;

class m260104_155023_create_tag_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60)->notNull(),
            'slug' => $this->string(80)->notNull()->unique(),
        ]);

        $this->createTable('{{%post_tag}}', [
            'post_id' => $this->integer()->notNull(),
            'tag_id'  => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk_post_tag', '{{%post_tag}}', ['post_id', 'tag_id']);

        $this->createIndex('idx_post_tag_tag_id', '{{%post_tag}}', 'tag_id');

        $this->addForeignKey(
            'fk_post_tag_post',
            '{{%post_tag}}',
            'post_id',
            '{{%post}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_post_tag_tag',
            '{{%post_tag}}',
            'tag_id',
            '{{%tag}}',
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
       $this->dropForeignKey('fk_post_tag_tag', '{{%post_tag}}');
        $this->dropForeignKey('fk_post_tag_post', '{{%post_tag}}');

        $this->dropTable('{{%post_tag}}');
        $this->dropTable('{{%tag}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260104_155023_create_tag_tables cannot be reverted.\n";

        return false;
    }
    */
}
