<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;


final class Category extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%category}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['slug'], 'string', 'max' => 120],
            [['slug'], 'unique'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public function getPosts(): ActiveQuery
    {
        return $this->hasMany(Post::class, ['category_id' => 'id']);
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }
}
