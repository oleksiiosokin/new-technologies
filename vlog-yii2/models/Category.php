<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\Inflector;



class Category extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%category}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
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
        $behaviors = [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                // щоб не перезатирало slug при оновленні, якщо вже є
                'immutable' => true,
                'value' => function () {
                    $base = Inflector::slug((string)$this->name);
                    return $base !== '' ? $base : ('category-' . time());
                },
            ],
        ];

        // timestamps тільки якщо в таблиці реально є created_at/updated_at
        if ($this->hasAttribute('created_at') && $this->hasAttribute('updated_at')) {
            $behaviors[] = [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ];
        }

        return $behaviors;
    }

}
