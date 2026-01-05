<?php

use yii\db\Migration;

class m260105_003949_add_unique_tag_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE t1 FROM {{%tag}} t1
            INNER JOIN {{%tag}} t2
            WHERE t1.name = t2.name AND t1.id > t2.id
        ");

        $this->createIndex('ux_tag_name', '{{%tag}}', 'name', true);
    }

    public function safeDown()
    {
        $this->dropIndex('ux_tag_name', '{{%tag}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260105_003949_add_unique_tag_name cannot be reverted.\n";

        return false;
    }
    */
}
