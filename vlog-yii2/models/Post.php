<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class Post extends ActiveRecord
{
    public $imageFile;
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
            [['category_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['published_at'], 'safe'],
            [['title', 'slug', 'image_path'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['imageFile'], 'file',
            'skipOnEmpty' => true,
            'extensions' => 'png, jpg, jpeg, webp',
            'checkExtensionByMimeType' => false,
            'maxSize' => 5 * 1024 * 1024,
            ],
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

    public function beforeValidate(): bool
    {
        if (is_string($this->published_at)) {
            $raw = trim($this->published_at);

            if ($raw === '') {
                $this->published_at = null;
            } else {
                $ts = strtotime(str_replace('T', ' ', $raw));
                $this->published_at = ($ts !== false) ? $ts : null;
            }
        }

        return parent::beforeValidate();
    }


    public function uploadImage(): bool
    {
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');
        if (!$this->imageFile) {
            return true;
        }

        if (!$this->validate(['imageFile'])) {
            return false;
        }

        $dir = \Yii::getAlias('@webroot/uploads/posts');
        FileHelper::createDirectory($dir, 0775, true);

        $newName = uniqid('post_', true) . '.' . $this->imageFile->extension;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $newName;

        if (!$this->imageFile->saveAs($fullPath)) {
            $this->addError('imageFile', 'Не вдалося зберегти файл.');
            return false;
        }

        if (!empty($this->image_path)) {
            $oldFull = \Yii::getAlias('@webroot') . $this->image_path;
            if (is_file($oldFull)) {
                @unlink($oldFull);
            }
        }

        $this->image_path = '/uploads/posts/' . $newName;
        return true;
    }
}
