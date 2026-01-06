<?php

namespace app\models;

use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\db\Expression;
use Yii;


class Post extends ActiveRecord
{
    public ?string $tagsInput = null;
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
            [['category_id', 'title', 'content'], 'required'],
            [['category_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['tagsInput'], 'string'],
            [['tagsInput'], 'default', 'value' => ''],
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
        return $this->hasMany(Comment::class, ['post_id' => 'id'])
            ->andWhere(['parent_id' => null])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'value' => function () {
                    if (!empty($this->slug)) {
                        return $this->slug;
                    }
                    $base = Inflector::slug((string)$this->title);
                    return $base !== '' ? $base : ('post-' . time());
                },
            ],
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

    public function beforeDelete()
    {
        if (!empty($this->image_path)) {
            $full = Yii::getAlias('@webroot') . $this->image_path;
            if (is_file($full)) {
                @unlink($full);
            }
        }

        Yii::$app->db->createCommand()
            ->delete('{{%post_tag}}', ['post_id' => $this->id])
            ->execute();

        return parent::beforeDelete();
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->syncTagsFromInput();
    }

    private function syncTagsFromInput(): void
    {
        $names = $this->parseTagsInput($this->tagsInput ?? '');

        Yii::$app->db->createCommand()
            ->delete('{{%post_tag}}', ['post_id' => $this->id])
            ->execute();

        if (empty($names)) {
            return;
        }

        foreach ($names as $name) {
            $tagId = $this->getOrCreateTagId($name);

            Yii::$app->db->createCommand()
                ->insert('{{%post_tag}}', [
                    'post_id' => $this->id,
                    'tag_id'  => $tagId,
                ])->execute();
        }
    }

    private function parseTagsInput(string $input): array
    {
        $raw = preg_split('/,/', $input);
        $clean = [];

        foreach ($raw as $item) {
            $name = trim($item);
            if ($name === '') continue;
            $clean[] = $name;
        }

        $uniq = [];
        $seen = [];
        foreach ($clean as $name) {
            $k = mb_strtolower($name);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $uniq[] = $name;
        }

        return $uniq;
    }

    private function getOrCreateTagId(string $name): int
    {
        $tag = Tag::find()
            ->where(new Expression('LOWER([[name]]) = :n', [':n' => mb_strtolower($name)]))
            ->one();

        if ($tag) {
            return (int)$tag->id;
        }

        $tag = new Tag();
        $tag->name = $name;

        $baseSlug = Inflector::slug($name);
        $slug = $baseSlug ?: ('tag-' . time());

        $i = 2;
        while (Tag::find()->where(['slug' => $slug])->exists()) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        $tag->slug = $slug;

        if (!$tag->save()) {
            $tag2 = Tag::find()
                ->where(new Expression('LOWER([[name]]) = :n', [':n' => mb_strtolower($name)]))
                ->one();
            if ($tag2) return (int)$tag2->id;

            throw new \RuntimeException('Не вдалося створити тег: ' . json_encode($tag->errors, JSON_UNESCAPED_UNICODE));
        }

        return (int)$tag->id;
    }

    


}
