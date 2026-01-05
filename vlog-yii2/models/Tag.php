<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

final class Tag extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%tag}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['slug'], 'string', 'max' => 80],
            [['slug'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    public function getPosts(): ActiveQuery
    {
        return $this->hasMany(Post::class, ['id' => 'post_id'])
            ->viaTable('{{%post_tag}}', ['tag_id' => 'id']);
    }
}
