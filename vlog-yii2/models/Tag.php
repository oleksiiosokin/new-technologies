<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\Inflector;


class Tag extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%tag}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
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

    public function behaviors(): array
    {
        $behaviors = [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => true,
                'value' => function () {
                    $base = Inflector::slug((string)$this->name);
                    return $base !== '' ? $base : ('tag-' . time());
                },
            ],
        ];

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
