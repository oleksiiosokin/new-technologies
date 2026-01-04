<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Post extends ActiveRecord
{
    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;

    public static function tableName(): string
    {
        return '{{%post}}';
    }

    public function rules(): array
    {
        return [
            [['category_id', 'title', 'slug', 'content'], 'required'],
            [['category_id', 'status', 'published_at', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title', 'slug', 'image_path'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%post_tag}}', ['post_id' => 'id']);
    }

    public function getComments(): ActiveQuery
    {
        // тільки кореневі коментарі (без відповідей)
        return $this->hasMany(Comment::class, ['post_id' => 'id'])
            ->andWhere(['parent_id' => null])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }
}
