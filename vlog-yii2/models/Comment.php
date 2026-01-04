<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

final class Comment extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%comment}}';
    }

    public function rules(): array
    {
        return [
            [['post_id', 'author_name', 'content', 'created_at'], 'required'],
            [['post_id', 'parent_id', 'status', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['author_name'], 'string', 'max' => 80],
            [['author_email'], 'string', 'max' => 120],

            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['post_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    public function getPost(): ActiveQuery
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

    public function getParent(): ActiveQuery
    {
        return $this->hasOne(Comment::class, ['id' => 'parent_id']);
    }

    public function getReplies(): ActiveQuery
    {
        return $this->hasMany(Comment::class, ['parent_id' => 'id'])
            ->orderBy(['created_at' => SORT_ASC]);
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class => [
                'updatedAtAttribute' => false,
            ],
        ];
    }
}
